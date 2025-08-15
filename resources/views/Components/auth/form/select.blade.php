@php
    $name = $attributes->get('name');
    $id = $attributes->get('id', $name);
    $options = $attributes->get('options', []);
    $placeholder = $attributes->get('placeholder');
    $label =
        trim($slot) !== '' ? trim($slot) : $attributes->get('label') ?? ucfirst(str_replace('_', ' ', (string) $name));
    $required = $attributes->has('required');
    $base = 'block w-full rounded-md bg-white px-3 py-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6';

    // Priority: old() → selected → value 
    $current = old($name, $attributes->get('selected', $attributes->get('value')));
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm/6 font-medium text-gray-900">{{ $label }}</label>

    <div class="mt-2">
        <select id="{{ $id }}" name="{{ $name }}" @if ($required) required @endif
            {{ $attributes->merge(['class' => $base])->except(['label', 'options', 'placeholder', 'value']) }}>
            @if ($placeholder)
                <option value="" disabled @selected($current === null || $current === '')>{{ $placeholder }}</option>
            @endif

            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected((string) $current === (string) $value)>{{ $text }}</option>
            @endforeach
        </select>

        @error($name)
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
