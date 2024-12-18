@extends('layout.dashboard')


@section('content')
    <div class="font-inter py-4">
        <p class="text-xl font-semibold text-gray-600">Welcome, {{ Auth::user()->name }}!</p>
        <p class="font-normal text-gray-700">Ini merupakan dashboard untuk mengelola isi konten website MangaLo!. Jadi,
            tolong <strong class="text-black">gunakan sebaik mungkin.</strong></p>
    </div>
    <div class="pb-4">
        <h1 class="text-xl font-semibold text-gray-600">Statistik</h1>
    </div>

    <div class="flex flex-wrap md:flex-nowrap gap-4">
        <div
            class="bg-blue-500 text-white rounded-lg w-full md:w-1/3 lg:w-1/4 shadow-lg p-4 hover:-translate-y-2 duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div id="manga-counter" class="text-4xl font-bold">{{ $manyManga }}</div>
                <i class="fa-solid fa-newspaper text-5xl"></i>
            </div>
            <div class="mt-4 text-lg">Manga</div>
            <a href="{{ route('List Manga') }}">
                <div
                    class="mt-2 bg-blue-600 py-2 px-4 rounded-b-lg text-center text-white cursor-pointer hover:bg-blue-700">
                    More info →
                </div>
            </a>
        </div>

        <div
            class="bg-red-500 text-white rounded-lg w-full md:w-1/3 lg:w-1/4 shadow-lg p-4 hover:-translate-y-2 duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div id="genre-counter" class="text-4xl font-bold">{{ $manyGenre }}</div>
                <i class="fa-solid fa-scroll text-5xl"></i>
            </div>
            <div class="mt-4 text-lg">Genre</div>
            <a href="{{ route('GenreList') }}">
                <div class="mt-2 bg-red-600 py-2 px-4 rounded-b-lg text-center text-white cursor-pointer hover:bg-red-700">
                    More info →
                </div>
            </a>
        </div>

        <div
            class="bg-yellow-500 text-white rounded-lg w-full md:w-1/4 lg:w-1/4 shadow-lg p-4 hover:-translate-y-2 duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div id="users-counter" class="text-4xl font-bold">{{ $manyStaff }}</div>
                <i class="fa-solid fa-users text-5xl"></i>
            </div>
            <div class="mt-4 text-lg">Users</div>
            <a href="{{ route('List.Staff') }}">
                <div
                    class="mt-2 bg-yellow-600 py-2 px-4 rounded-b-lg text-center text-white cursor-pointer hover:bg-yellow-700">
                    More info →
                </div>
            </a>
        </div>

        <div
            class="bg-green-500 text-white rounded-lg w-full md:w-1/4 lg:w-1/4 shadow-lg p-4 hover:-translate-y-2 duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between">
                <div id="blogs-counter" class="text-4xl font-bold">{{ $manyBlogs }}</div>
                <i class="fa-regular fa-note-sticky text-5xl"></i>
            </div>
            <div class="mt-4 text-lg">Blogs</div>
            <a href="{{ route('List Blogs') }}">
                <div
                    class="mt-2 bg-green-600 py-2 px-4 rounded-b-lg text-center text-white cursor-pointer hover:bg-green-700">
                    More info →
                </div>
            </a>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Data dari server
            const counters = {
                manga: {{ $manyManga }},
                genre: {{ $manyGenre }},
                users: {{ $manyStaff }},
                blogs: {{ $manyBlogs }}
            };

            // Fungsi untuk animasi penghitung
            function animateCounter(elementId, targetNumber, duration) {
                const element = document.getElementById(elementId);
                const stepTime = Math.abs(Math.floor(duration / targetNumber));
                let currentNumber = 0;

                const increment = () => {
                    currentNumber += 1;
                    element.textContent = currentNumber;
                    if (currentNumber < targetNumber) {
                        setTimeout(increment, stepTime);
                    }
                };

                increment();
            }

            // Menjalankan animasi untuk setiap counter
            animateCounter("manga-counter", counters.manga, 1000); // 2 detik
            animateCounter("genre-counter", counters.genre, 1000);
            animateCounter("users-counter", counters.users, 1000);
            animateCounter("blogs-counter", counters.blogs, 1000);
        });
    </script>
@endsection
