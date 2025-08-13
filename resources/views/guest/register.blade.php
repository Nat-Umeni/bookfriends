@extends('layouts.auth')

@section('title', 'Register - Bookfriends')

@section('auth-content')
    <div>
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900">Register an account</h2>
        <p class="mt-2 text-sm/6 text-gray-500">
            Already a member?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Sign In Here!</a>
        </p>
    </div>

    <div class="mt-10">
        <form action="{{ route('register.store') }}" method="POST" class="space-y-6">
            @csrf

            <x-auth.form.input name="name" autocomplete="name" required>Name</x-auth.form.input>
            <x-auth.form.input name="email" type="email" autocomplete="email" required>Email address</x-auth.form.input>
            <x-auth.form.input name="password" type="password" autocomplete="new-password" required>Password</x-auth.form.input>
            <x-auth.form.input name="password_confirmation" type="password" required>Confirm Password</x-auth.form.input>

            <x-auth.form.button>Create Account</x-auth.form.button>
        </form>

        <x-auth.oauth.section title="Or register with" google="#" github="#" />
    </div>
@endsection
