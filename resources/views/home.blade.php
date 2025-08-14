@extends('layouts.app')

@section('title', 'Bookfriends')
@section('header', 'Home')

@section('content')
    <div class="mt-8">
        @guest
            <p>Welcome to Bookfriends!</p>
            <p>Sign up to get started</p>
        @endguest

        @auth
            <p>Welcome back, {{ auth()->user()->name }}!</p>

            <div class="mt-8 space-y-6">
                @foreach ($sections as $section)
                    <x-books.section :status="$section['key']" :items="$section['items']">
                        {{ $section['label'] }}
                    </x-books.section>
                @endforeach
            </div>
        @endauth
    </div>
@endsection
