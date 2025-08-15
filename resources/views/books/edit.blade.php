@extends('layouts.app')

@section('title', 'Bookfriends')
@section('header', 'Edit a Book')

@section('content')
    <div class="mt-8">
        <p>Change the details of {{ $book->title }}</p>

        <div class="mt-10">
            <form action="{{ route('books.update', $book->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <x-auth.form.input name="title" autocomplete="off" value="{{ $book->title }}">Name</x-auth.form.input>
                <x-auth.form.input name="author" autocomplete="off" value="{{ $book->author }}">Author</x-auth.form.input>
                <x-auth.form.select name="status" :options="$statuses" placeholder="Select a status"
                    :selected="$selectedStatus">Status</x-auth.form.select>
                <x-auth.form.button>Edit Book</x-auth.form.button>
            </form>
        </div>
    </div>
@endsection
