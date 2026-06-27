<div x-data="{
    show: false,
    message: '',
    type: 'success',
    init() {
        Livewire.on('show-notification', msg => {
            this.message = msg;
            this.type = 'success';
            this.show = true;
            setTimeout(() => this.show = false, 5000);
        });

        Livewire.on('show-error', msg => {
            this.message = msg;
            this.type = 'error';
            this.show = true;
            setTimeout(() => this.show = false, 5000);
        });
    }
}" class="relative">
    <div x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-bind:class="{
            'bg-green-50 border-green-500 text-green-800': type === 'success',
            'bg-red-50 border-red-500 text-red-800': type === 'error'
        }"
        class="fixed top-5 right-5 w-72 p-4 border-l-4 rounded-lg shadow-lg z-50" x-cloak>
        <div class="flex items-start gap-2">
            <span class="material-symbols-outlined text-base" x-text="type === 'success' ? 'check_circle' : 'error'"
                :class="type === 'success' ? 'text-green-600' : 'text-red-600'"></span>
            <div class="flex-1 text-sm font-medium" x-text="message"></div>
            <button @click="show = false" class="transition-colors"
                :class="type === 'success' ? 'text-green-600 hover:text-green-900' : 'text-red-600 hover:text-red-900'">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
    </div>
</div>
