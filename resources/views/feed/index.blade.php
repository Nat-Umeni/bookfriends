@extends('layouts.app')

@section('title', 'Bookfriends')
@section('header', 'Feed')

@section('content')
    <div class="mt-8">
        <div class="mt-8 space-y-6">
            <x-section>
                <x-slot:content>
                    @foreach ($books as $book)
                        <x-card :title="trim(($book->friend_name ?? 'Someone') . ' ' . ($book->action ?? ''))" :subtitle="trim($book->title . ($book->author ? ' by ' . $book->author : ''))">
                            <x-slot:actions>
                                <span class="text-sm text-slate-500">
                                    {{ optional(\Illuminate\Support\Carbon::parse($book->pivot_updated_at ?? $book->updated_at))->diffForHumans() }}
                                </span>
                            </x-slot:actions>
                        </x-card>
                    @endforeach
                </x-slot:content>
            </x-section>
        </div>
    </div>
@endsection
