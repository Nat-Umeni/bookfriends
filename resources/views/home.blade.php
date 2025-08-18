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
                    @continue($section['books']->isEmpty())

                    <x-section :status="$section['key']">
                        {{ $section['label'] }}
                        <x-slot:content>
                            @foreach ($section['books'] as $book)
                                <x-card :title="$book->title" :subtitle="$book->author ? 'by ' . $book->author : null">
                                    <x-slot:actions>
                                        <a class="text-blue-500" href="{{ route('books.edit', $book->id) }}">Edit</a>
                                    </x-slot:actions>
                                </x-card>
                            @endforeach
                        </x-slot:content>
                    </x-section>
                @endforeach
            </div>
        @endauth
    </div>
@endsection
