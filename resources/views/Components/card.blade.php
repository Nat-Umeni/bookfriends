@php
    $heading = isset($title) && trim($title) !== '' ? trim($title) : $attributes->get('title');
    $sub = isset($subtitle) && trim($subtitle) !== '' ? trim($subtitle) : $attributes->get('subtitle');

    $dataTest = $attributes->get('data-test');
@endphp

<div {{ $attributes->class('bg-slate-100 p-6 rounded flex justify-between items-center') }}
    @if ($dataTest) data-test="{{ $dataTest }}" @endif>
    <div class="min-w-0">
        @if ($heading)
            <h3 class="font-bold text-lg text-slate-800" data-role="card-title">{{ $heading }}</h3>
        @endif

        @if ($sub)
            <div class="text-slate-600 text-sm mt-1">{{ $sub }}</div>
        @endif

        @isset($slot)
            <div class="mt-3">
                {{ $slot }}
            </div>
        @endisset
    </div>

    @isset($actions)
        <div class="ml-4 shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div>
