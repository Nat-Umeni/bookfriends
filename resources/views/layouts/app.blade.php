@extends('layouts.base')

@section('body')
    <div class="max-w-4xl mx-auto px-6 grid grid-cols-8 gap-12 mt-16">
        <aside class="col-span-2 border-r border-slate-200 space-y-4">
            @guest
                <ul>
                    <li>
                        <a href="{{ route('home') }}" class="font-bold text-lg text-slate-600 hover:text-slate-800 block">Home</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ route('register') }}"
                            class="font-bold text-lg text-slate-600 hover:text-slate-800 block">Register</a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}"
                            class="font-bold text-lg text-slate-600 hover:text-slate-800 block">Login</a>
                    </li>
                </ul>
            @endguest

            @auth
                <ul>
                    <li>
                        <span class="font-bold text-lg text-slate-600 hover:text-slate-800 block">
                            {{ auth()->user()->name }}
                        </span>
                    </li>
                    <li>
                        <a href="{{ route('feed.index') }}" class="font-bold text-lg text-slate-600 hover:text-slate-800 block">Feed</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <a href="{{ route('home') }}" class="font-bold text-lg text-slate-600 hover:text-slate-800 block">My Books</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <a href="{{ route('books.create') }}"
                            class="font-bold text-lg text-slate-600 hover:text-slate-800 block">Add a Book</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <a href="{{ route('friends.index') }}"
                            class="font-bold text-lg text-slate-600 hover:text-slate-800 block">Friends</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="font-bold text-lg text-slate-600 hover:text-slate-800 block">Logout</button>
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
