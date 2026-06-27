@props([
    'submitAction' => 'submit',
    'submitText' => 'Submit',
])

<form {{ $attributes->merge(['class' => 'space-y-4']) }} wire:submit.prevent="{{ $submitAction }}">
    {{ $slot }}

    <div class="flex justify-end gap-2">

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-800">
            {{ $submitText }}
        </button>
    </div>
</form>
