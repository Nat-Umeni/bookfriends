@php
    $name = $attributes->get('name');
    $type = $attributes->get('type', 'text');
    $id = $attributes->get('id', $name);
    $autocomplete = $attributes->get('autocomplete');
    $label =
        trim($slot) !== '' ? trim($slot) : $attributes->get('label') ?? ucfirst(str_replace('_', ' ', (string) $name));
    $base =
        'block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm/6 font-medium text-gray-900">{{ $label }}</label>
    <div class="mt-2">
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($attributes->has('required')) required @endif {{-- merge classes; strip label so it doesn't land on the input --}}
            {{ $attributes->merge(['class' => $base])->except(['label']) }}
            @if (!$attributes->has('value') && $type !== 'password') value="{{ old($name) }}" @endif />
        @error($name)
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
