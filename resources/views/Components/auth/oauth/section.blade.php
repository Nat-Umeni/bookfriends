@php
    $title = $attributes->get('title', 'Or continue with');
    $google = $attributes->get('google', '#');
    $github = $attributes->get('github', '#');
@endphp

<div class="mt-10">
    <div class="relative">
        <div aria-hidden="true" class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm/6 font-medium">
            <span class="bg-white px-6 text-gray-900">{{ $title }}</span>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4">
        <x-auth.oauth.google :href="$google" />
        <x-auth.oauth.github :href="$github" />
    </div>
</div>
