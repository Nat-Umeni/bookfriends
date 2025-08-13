@extends('layouts.base')

@section('body')
  <div class="flex min-h-full">
    <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
      <div class="mx-auto w-full max-w-sm lg:w-96">
        @yield('auth-content')
      </div>
    </div>

    <div class="relative hidden w-0 flex-1 lg:block">
      <img
        src="https://images.unsplash.com/photo-1496917756835-20cb06e75b4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1908&q=80"
        alt=""
        class="absolute inset-0 size-full object-cover"
      />
    </div>
  </div>
@endsection
