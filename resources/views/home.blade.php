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
        <x-books.section :items="$wantToRead">Books you want to read</x-books.section>
        <x-books.section :items="$reading">Books you are currently reading</x-books.section>
        <x-books.section :items="$read">Books you've read</x-books.section>
      </div>
    @endauth
  </div>
@endsection

