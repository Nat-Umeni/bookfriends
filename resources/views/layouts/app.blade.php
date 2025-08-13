@extends('layouts.base')

@section('body')
    <div class="max-w-4xl mx-auto px-6 grid grid-cols-8 gap-12 mt-16">
        <aside class="col-span-2 border-r border-slate-200 space-y-6">
            @guest
                <ul>
                    <li>
                        <a href="{{ route('home') }}"
                           class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">Home</a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="{{ route('register') }}"
                           class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">Register</a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}"
                           class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">Login</a>
                    </li>
                </ul>
            @endguest

            @auth
                <ul>
                    <li>
                        <span class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">
                            {{ auth()->user()->name }}
                        </span>
                    </li>
                    <li>
                        <a href="#" class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">Feed</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <a href="#" class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">My Books</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <a href="#" class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">Add a Book</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <a href="#" class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">Friends</a>
                    </li>
                </ul>

                <ul>
                    <li>
                        <a href="#" class="font-bold text-lg text-slate-600 hover:text-slate-800 block py-1">Logout</a>
                    </li>
                </ul>
            @endauth
        </aside>

        <main class="col-span-6">
            @hasSection('header')
                <h1 class="text-2xl font-bold text-slate-800">@yield('header')</h1>
            @endif

            @yield('content')
        </main>
    </div>
@endsection
