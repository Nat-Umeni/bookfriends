@php
    $statusKey = $attributes->get('status');
    $dataTest = $attributes->get('data-test') ?: ($statusKey ? "section-{$statusKey}" : null);
@endphp

<section {{ $attributes->class('')->except(['status', 'empty', 'data-test']) }} data-test="{{ $dataTest }}">
    <h2 class="text-xl font-bold text-slate-600">{{ trim($slot) }}</h2>

    <div class="mt-4 space-y-3">
        @isset($content)
            {{ $content }}
        @else
            <p class="text-slate-500">{{ $emptyText }}</p>
        @endisset
    </div>
</section>
