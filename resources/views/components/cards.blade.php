@props(['initial', 'bgClass'])

<div
    {{ $attributes->merge([
        'class' =>
            'p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-by-force transition-all duration-200 ease-in-out border border-gray-100 dark:border-gray-700',
    ]) }}>
    {{ $slot }}
</div>
