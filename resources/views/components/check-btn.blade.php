<div class="flex items-center gap-5">
    <div x-data="{
        glow: localStorage.getItem('glow') === 'true',
        toggleGlow() {
            this.glow = !this.glow;
            localStorage.setItem('glow', this.glow);
        }
    }" class="flex items-center">

        <!-- Glow Toggle Button with dynamic color -->
        <button @click="toggleGlow"
            :class="glow
                ?
                'bg-red-600 hover:bg-red-700' :
                'bg-green-600 hover:bg-green-700'"
            class="px-2 py-2 text-white text-base font-semibold rounded-lg transition items-center">
            <span x-text="glow ? 'Check-Out' : 'Check-In'"></span>
        </button>
    </div>

    <!-- Glow Indicator -->
    <div class="relative w-4 h-4 rounded-full bg-[#d01414] overflow-hidden shadow" x-init="$watch('glow', value => localStorage.setItem('glow', value))">
        <span x-show="glow"
            class="absolute inset-0 rounded-full bg-[#32d874] pointer-events-none shadow-[0_0_10px_#178a45,0_0_20px_#178a45] transition-opacity duration-300">
        </span>
    </div>
</div>
