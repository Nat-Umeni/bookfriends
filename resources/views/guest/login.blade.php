@extends('layouts.auth')

@section('title', 'Sign In - Bookfriends')

@section('auth-content')
    <div>
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
        <p class="mt-2 text-sm/6 text-gray-500">
            Not a member?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Register Here!</a>
        </p>
    </div>

    <div class="mt-10">
        <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
            @csrf
            <x-auth.form.input name="email" type="email" autocomplete="email" required>Email address</x-auth.form.input>
            <x-auth.form.input name="password" type="password" autocomplete="current-password"
                required>Password</x-auth.form.input>
            <x-auth.form.button>Sign in</x-auth.form.button>
        </form>
        <x-auth.oauth.section title="Or sign in with" google="{{ route('auth.oauth.redirect', ['provider' => 'google']) }}"
            github="{{ route('auth.oauth.redirect', ['provider' => 'github']) }}" />
    </div>
@endsection
