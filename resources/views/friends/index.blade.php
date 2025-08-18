@extends('layouts.app')

@section('title', 'Bookfriends')
@section('header', 'Friends')

@section('content')
    <div class="mt-8">

        @if ($pendingRequests->isEmpty() || $requestingFriends->isEmpty() || $friends->isEmpty())
            <p>You have no friends at the moment add some below and check back later!</p>
        @endif

        <div class="mt-8 space-y-6">

            <div class="my-8">
                <form action="{{ route('friends.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <x-auth.form.input name="email" autocomplete="off">Your Friend's Email</x-auth.form.input>
                    <x-auth.form.button>Add Friend</x-auth.form.button>
                </form>
            </div>

            @if ($pendingRequests->isNotEmpty())
                <x-section>
                    Pending Friend Requests
                    <x-slot:content>
                        @foreach ($pendingRequests as $pendingFriend)
                            <x-card :title="$pendingFriend->name" :subtitle="$pendingFriend->email">
                                <x-slot:actions>
                                    <form method="POST" action="{{ route('friends.destroy', $pendingFriend->id) }}">
                                        @csrf @method('DELETE')
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
                            <x-card :title="$requestingFriend->name" :subtitle="$requestingFriend->email">
                                <x-slot:actions>
                                        <form method="POST" action="{{ route('friends.destroy', $requestingFriend->id) }}">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:cursor-pointer">Deny</button>
                                        </form>
                                        <form method="POST" action="{{ route('friends.update', $requestingFriend->id) }}">
                                            @csrf @method('PATCH')
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
                            <x-card :title="$friend->name" :subtitle="$friend->email">
                                <x-slot:actions>
                                    <form method="POST" action="{{ route('friends.destroy', $friend->id) }}">
                                        @csrf @method('DELETE')
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
