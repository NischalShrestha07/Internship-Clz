@props([
    'placeholder' => 'Search by Employee',
])

<input type="text"
    {{ $attributes->merge([
        'placeholder' => $placeholder,
        'class' =>
            'px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md w-56',
    ]) }} />
