@props([
    'columns' => 1,
    'gap' => 'gap-6',
])

<form {{ $attributes->merge(['class' => 'space-y-6']) }}>
    <div class="grid grid-cols-1 {{ $columns >= 2 ? 'sm:grid-cols-2' : '' }} {{ $columns >= 3 ? 'lg:grid-cols-3' : '' }} {{ $gap }}">
        {{ $slot }}
    </div>
</form>
