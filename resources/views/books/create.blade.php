@extends('layouts.app')

@section('title', 'Bookfriends')
@section('header', 'Add a Book')

@section('content')
    <div class="mt-8">
        <p>Just add the name and author of the book, and show it off to the world.</p>

        <div class="mt-10">
            <form action="{{ route('books.store') }}" method="POST" class="space-y-6">
                @csrf
                <x-auth.form.input name="title" autocomplete="off" >Name</x-auth.form.input>
                <x-auth.form.input name="author" autocomplete="off" >Author</x-auth.form.input>
                <x-auth.form.select 
                    name="status" 
                    :options="$statuses"
                    placeholder="Select a status"
                    
                >Status</x-auth.form.select>
                
                <x-auth.form.button>Create Book</x-auth.form.button>
            </form>
        </div>
    </div>
@endsection
