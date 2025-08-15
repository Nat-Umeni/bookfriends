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
                    <x-books.section :status="$section['key']">
                        {{ $section['label'] }}

                        <x-slot:content>
                            @forelse ($section['books'] as $book)
                                <x-books.card :book="$book">
                                    <x-slot:links>
                                        <a class="text-blue-500" href="{{ route('books.edit', $book->id) }}">Edit</a>
                                    </x-slot:links>
                                </x-books.card>
                            @empty
                                <p class="text-slate-500">No books yet.</p>
                            @endforelse
                        </x-slot:content>
                    </x-books.section>
                @endforeach
            </div>
        @endauth
    </div>
@endsection
