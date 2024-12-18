<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\MangaSwiperController;
use App\Http\Middleware\Dashboard;
use App\Http\Middleware\MangaPurchase;
use App\Http\Middleware\RestrictStaffAccess;
use App\Models\Blog;
use App\Models\Chapter;
use App\Models\genre;
use App\Models\User;
use App\Models\Manga;
use App\Models\MangaSwiper;
use Illuminate\Support\Facades\Storage;

//  ----------------------------------------------------------------
// ROUTE VIEW LANDING PAGE
//  ----------------------------------------------------------------
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('join', function () {
    return view('join', ['title' => 'MangaLo | Join Us']);
})->name('join');


Route::get('faq', function () {
    return view('faq', array('title' => 'MangaLo | FAQ'));
})->name('faq');


Route::get('blogs', function () {
    $blogs = Blog::all();

    return view('blogs', array('title' => 'MangaLo | Blogs', 'blogs' => $blogs));
})->name('blogs');

Route::get('blog/{id}', action: function ($id) {
    $blog = Blog::where('id', '=', $id)->get()->first();
    if (!$blog) {
        abort(404);
    }
    return view('blog', array('title' => 'MangaLo | blog', 'blog' => $blog));
})->name('blog');

// Route::get('list', function () {
//     $mangas = Manga::orderBy('updated_at', 'desc')->paginate(8);
//     return view('list', array('title' => 'MangaLo | List', 'mangas' => $mangas));
// })->name('list');

Route::get('register', function () {
    return view('register.signup',);
})->name('register');

Route::get('login', function () {
    return view('register.login',);
})->name('login');

Route::get('forgot', function () {
    return view('register.forgot', data: array('title' => 'MangaLo | Forgot'));
})->name('forgot');

Route::get('chapter/{id}', [ChapterController::class, 'view'])
    ->middleware(MangaPurchase::class)
    ->name('chapter');
Route::get('manga/{id}', [MangaController::class, 'view'])
->name('manga');
// Route::post('manga/{id}/unlock', [MangaController::class, 'unlock'])->name('manga.unlock')->middleware('auth');

Route::get('/search-manga', [MangaController::class, 'search'])->name('search.manga');
Route::get('list', [MangaController::class, 'sort'])->name('list');

Route::controller(GenreController::class)->group(function () {
    Route::get('genre/search', 'search')->name('genre.search');
    Route::get('/genre/{id}', 'sortGenres')->name('genre.sort');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/coins/topup', [CoinController::class, 'showTopUpPage'])->name('coins.topup');
    Route::post('/coins/topup/kocak', [CoinController::class, 'processTopUp'])->name('process.topup');
    Route::post('/manga/{manga}/unlock', [CoinController::class, 'unlockManga'])->name('manga.unlock');
    Route::get('checkout', function () {
        return view('checkout', array('title' => 'MangaLo | Checkout'));
    })->name('checkout');
    Route::post('/payment/success', [CoinController::class, 'successPayment'])->name('payment.success');
    Route::get('purchased', function () {
        $user = Auth::user();
        $purchasedMangas = Manga::whereHas('purchases', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('updated_at', 'desc')->get();

        return view('purchased', [
            'title' => 'MangaLo | Purchased',
            'mangas' => $purchasedMangas,
        ]);
    })->name('purchased');
});
//  ----------------------------------------------------------------
// AUTHENTICATION
//  ----------------------------------------------------------------

Route::controller(Authcontroller::class)->group(function () {
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
    Route::post('forgot-password', 'forgotPassword')->name('password.email');
    Route::get('reset-password/{token}', function ($token) {
        return view('register.reset', ['token' => $token], array('title' => 'Reset'));
    })->name('password.reset');
    Route::post('reset-password',  'reset')->name('password.update');
});

//  ----------------------------------------------------------------
// DASHBOARD
//  ----------------------------------------------------------------]

Route::middleware(Dashboard::class)->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware('auth')->name('dashboard');

    Route::get('points', function () {
        return view('points', array('title' => 'MangaLo! | Points'));
    })->name('points');

    // Blog Routes Group
    Route::controller(BlogController::class)->group(function () {
        Route::get('BlogsList',  'view')->name('List Blogs');
        Route::get('BlogCreate', 'create')->name('Create blog');
        Route::delete('blog/delete/{id}', 'delete')->name('blog.delete');
        Route::post('blog/submit', 'store')->name('blog.submit');
        Route::put('blog/update/{id}', 'update')->name('blog.update');
    });

    Route::controller(GenreController::class)->group(function () {
        Route::get('GenreList', [GenreController::class, 'index'])->name('GenreList');
        Route::post('GenreStore', [GenreController::class, 'store'])->name('GenreStore');
        Route::get('GenreEdit/{id}', [GenreController::class, 'edit'])->name('GenreEdit');
        Route::put('GenreUpdate/{id}', [GenreController::class, 'update'])->name('GenreUpdate');
        Route::delete('GenreDelete/{id}', [GenreController::class, 'destroy'])->name('GenreDelete');
    });


    // Manga Routes Group
    Route::controller(MangaController::class)->group(function () {
        Route::get('MangaList', 'index')->name('List Manga');
        Route::get('MangaCreate', 'create')->name('Create manga');
        Route::post('MangaStore', 'store')->name('Store manga');
        Route::get('MangaEdit/{manga}', 'edit')->middleware(RestrictStaffAccess::class)->name('Edit manga');
        Route::put('MangaUpdate/{manga}', 'update')->name('Update manga');
        Route::delete('MangaDelete/{manga}', 'destroy')->name('Delete manga');
        Route::get('MangaDetail/{manga}', function () {
            return view('dashboard.manga.detail', data: array('title' => 'Dashboard | Manga Detail'));
        })->name('Detail Manga');
    });

    // Staff Routes Group
    Route::controller(StaffController::class)
        ->middleware(RestrictStaffAccess::class)
        ->group(function () {
            Route::get('StaffList', 'index')->name('List.Staff');
            Route::get('StaffCreate', 'showForm')->name('Staff.create');
            Route::post('staffSubmit', 'addStaff')->name('staff.submit');
            Route::delete('staffDelete/{id}', 'delete')->name('staff.delete');
        });

    Route::controller(MangaSwiperController::class)->group(function () {
        Route::get('swiper-list', 'index')->name('swiper.list');
        Route::post('swiper-submit', 'store')->name('swiper.submit');
        Route::delete('swiper-delete/{id}', 'destroy')->name('swiper.delete');
        Route::patch('swiper-toggle-active/{id}', 'toggleActive')->name('swiper.toggle');
    });

    Route::prefix('manga/{mangaId}/chapters')->group(function () {
        Route::get('/', [ChapterController::class, 'index'])->name('chapters.index');
        Route::get('/create', [ChapterController::class, 'create'])->name('chapters.create');
        Route::post('/', [ChapterController::class, 'store'])->name('chapters.store');
        Route::delete('/{id}', [ChapterController::class, 'destroy'])->name('chapters.destroy');
        Route::get('/{id}/edit', [ChapterController::class, 'edit'])->name('chapters.edit');
        Route::put('/{id}', [ChapterController::class, 'update'])->name('chapters.update');
    });
});
