@extends('layouts.app')

@section('title', 'Bookfriends')
@section('header', 'Friends')

@section('content')
    <div class="mt-8">
        @guest
            <p>Welcome to Bookfriends!</p>
            <p>Sign up to get started</p>
        @endguest

        @auth
            <p>All of your friends:</p>

            <div class="mt-8 space-y-6">
                <x-section>
                    Pending Friend Requests
                    <x-slot:content>
                        @foreach ($pendingRequests as $pendingFriend)
                            <x-card :title="$pendingFriend->name">
                                <x-slot:actions>
                                    <form method="POST">
                                        @csrf
                                        <button class="text-green-600">Accept</button>
                                    </form>
                                </x-slot:actions>
                            </x-card>
                        @endforeach
                    </x-slot:content>
                </x-section>
            </div>
        @endauth
    </div>
@endsection
