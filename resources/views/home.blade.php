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
            <p>Welcome to Bookfriends, {{ auth()->user()->name }}!</p>
        @endauth
    </div>
@endsection
