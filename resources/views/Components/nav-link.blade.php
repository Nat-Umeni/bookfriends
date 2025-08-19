@props(['href', 'active' => false])

@php
    $base = 'font-bold text-lg block rounded px-3 py-2 transition-colors duration-350 ease-in-out';
    $inactive = 'text-slate-600 hover:text-indigo-600 hover:bg-gray-50';
    $activeCls = 'bg-gray-50 text-indigo-600';

    $classes = $active ? "$base $activeCls" : "$base $inactive";
@endphp

<a
    {{ $attributes->merge([
        'href' => $href,
        'class' => $classes,
        'aria-current' => $active ? 'page' : null,
    ]) }}>
    {{ $slot }}
</a>
