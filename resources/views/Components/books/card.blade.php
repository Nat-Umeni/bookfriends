@php
    $book = $attributes->get('book');
@endphp

<div class="bg-slate-100 p-6 rounded flex justify-between items-center">
    <div>
        <h2 class="font-bold text-lg text-slate-800" data-role="card-title">{{ $book->title }}</h2>

        @if (!empty($book->author))
            <div class="text-slate-600 text-sm">by {{ $book->author }}</div>
        @endif
    </div>

    @isset($links)
        <div>
            {{ $links }}
        </div>
    @endisset
</div>
