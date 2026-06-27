@props([
    'type' => 'submit',
    'color' => 'primary', // options: primary, secondary, danger
    'size' => 'md', // options: sm, md, lg
    'rounded' => 'xl',
    'disabled' => false,
])

@php
    $base =
        'inline-flex items-center rounded justify-center font-semibold focus:outline-none transition duration-150 ease-in-out';

    $colors = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-800',
        'secondary' => 'bg-gray-200 text-gray-800 hover:bg-gray-300',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
    ];

    $sizes = [
        'sm' => 'px-3 py-1 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-5 py-3 text-lg',
    ];

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

    $classes = "$base {$colors[$color]} {$sizes[$size]} rounded-{$rounded} $disabledClasses";
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}
    @if ($disabled) disabled @endif>
    {{ $slot }}
</button>
