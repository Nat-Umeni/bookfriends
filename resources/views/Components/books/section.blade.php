@php
    $items = $attributes->get('items', collect());
    $empty = $attributes->get('empty', 'No books yet.');
@endphp

<div>
    <h2 class="text-xl font-bold text-slate-600">{{ trim($slot) }}</h2>

    <div class="mt-4 space-y-3">
        @forelse ($items as $book)
            <x-books.card :book="$book" >
                <x-slot:links>
                    LINK!
                </x-slot:links>
            </x-books.card>
        @empty
            <p class="text-slate-500">{{ $empty }}</p>
        @endforelse
    </div>
</div>
