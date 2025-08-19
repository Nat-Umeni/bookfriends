@extends('layouts.base')

@section('body')
    <div class="max-w-4xl mx-auto px-6 grid grid-cols-8 gap-12 mt-16">
        <aside class="col-span-2 border-r border-slate-200 space-y-4 pr-4">
            @guest
                <ul>
                    <li>
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            Home
                        </x-nav-link>
                    </li>
                </ul>
                <ul>
                    <li>
                        <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                            Register
                        </x-nav-link>
                    </li>
                    <li>
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            Login
                        </x-nav-link>
                    </li>
                </ul>
            @endguest

            @auth
                <ul>
                    <li>
                        <span class="font-bold text-lg text-slate-600 block px-3 py-2">
                            {{ auth()->user()->name }}
                        </span>
                    </li>
                    <li>
                        <x-nav-link :href="route('feed.index')" :active="request()->routeIs('feed.index')">
                            Feed
                        </x-nav-link>
                    </li>
                </ul>

                <ul>
                    <li>
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            My Books
                        </x-nav-link>
                    </li>
                </ul>

                <ul>
                    <li>
                        <x-nav-link :href="route('books.create')" :active="request()->routeIs('books.create')">
                            Add a Book
                        </x-nav-link>
                    </li>
                </ul>

                <ul>
                    <li>
                        <x-nav-link :href="route('friends.index')" :active="request()->routeIs('friends.index')">
                            Friends
                        </x-nav-link>
                    </li>
                </ul>

                <ul>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full text-left font-bold text-lg rounded px-3 py-2  text-slate-600 hover:bg-gray-50 hover:text-red-500 hover:cursor-pointer transition-colors duration-300 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">Logout</button>
                        </form>
                    </li>
                </ul>
            @endauth
        </aside>

        <main class="col-span-6">
            @hasSection('header')
                <h1 class="text-2xl font-bold text-slate-600">@yield('header')</h1>
            @endif

            @yield('content')
        </main>
    </div>
@endsection
