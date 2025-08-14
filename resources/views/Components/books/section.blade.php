@php
    $items      = $attributes->get('items', collect());
    $emptyText  = $attributes->get('empty', 'No books yet.');
    $statusKey  = $attributes->get('status');

    // If caller didn't provide data-test but did provide a status, auto-generate it.
    $dataTest   = $attributes->get('data-test');
    if (!$dataTest && $statusKey) {
        $dataTest = "section-{$statusKey}";
    }

    // Build wrapper attributes
    $wrapperAttributes = ['class' => ''];
    if ($dataTest) {
        $wrapperAttributes['data-test'] = $dataTest;
    }
@endphp

<section {{ $attributes->merge($wrapperAttributes)->except(['items','empty','status']) }}>
    <h2 class="text-xl font-bold text-slate-600">{{ trim($slot) }}</h2>

    <div class="mt-4 space-y-3">
        @forelse ($items as $book)
            <x-books.card :book="$book">
                @isset($links)
                    <x-slot:links>{{ $links }}</x-slot:links>
                @endisset
            </x-books.card>
        @empty
            <p class="text-slate-500">{{ $emptyText }}</p>
        @endforelse
    </div>
</section>
