@extends('layouts.app')

@section('title', 'Bookfriends')
@section('header', 'Friends')

@section('content')
    <div class="mt-8">

        @if ($pendingRequests->isNotEmpty() || $requestingFriends->isNotEmpty() || $friends->isNotEmpty())
            <p>All of your friends:</p>
        @else
            <p>You have no friends or friend requests yet, get networking!</p>
        @endif

        <div class="mt-8 space-y-6">

                <p>You can add a friend by email below</p>

                <div class="my-8">
                    <form action="{{ route('friends.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <x-auth.form.input name="email" autocomplete="off">Your Friends Email</x-auth.form.input>
                        <x-auth.form.button>Add Friend</x-auth.form.button>
                    </form>
                </div>

            @if ($pendingRequests->isNotEmpty())
                <x-section>
                    Pending Friend Requests
                    <x-slot:content>
                        @foreach ($pendingRequests as $pendingFriend)
                            <x-card :title="$pendingFriend->name">
                                <x-slot:actions>
                                    <form method="POST">
                                        @csrf
                                        <button class="text-red-600 hover:cursor-pointer">Cancel</button>
                                    </form>
                                </x-slot:actions>
                            </x-card>
                        @endforeach
                    </x-slot:content>
                </x-section>
            @endif

            @if ($requestingFriends->isNotEmpty())
                <x-section>
                    Incoming Friend Requests
                    <x-slot:content>
                        @foreach ($requestingFriends as $requestingFriend)
                            <x-card :title="$requestingFriend->name">
                                <x-slot:actions>
                                    <form method="POST">
                                        @csrf
                                        <button class="text-green-600 hover:cursor-pointer">Accept</button>
                                    </form>
                                </x-slot:actions>
                            </x-card>
                        @endforeach
                    </x-slot:content>
                </x-section>
            @endif

            @if ($friends->isNotEmpty())
                <x-section>
                    Friends
                    <x-slot:content>
                        @foreach ($friends as $friend)
                            <x-card :title="$friend->name">
                                <x-slot:actions>
                                    <form method="POST">
                                        @csrf
                                        <button class="text-red-600 hover:cursor-pointer">Remove</button>
                                    </form>
                                </x-slot:actions>
                            </x-card>
                        @endforeach
                    </x-slot:content>
                </x-section>
            @endif

        </div>
    </div>
@endsection
