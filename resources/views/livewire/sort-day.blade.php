 <div class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
                <div x-data="{ open: false, selected: 'Last 30 days' }" class="relative w-48">
                    <button @click="open = !open" type="button"
                        class="flex justify-between items-center w-full p-2 border border-black rounded text-center bg-white text-black">
                        <span x-text="selected"></span>
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </button>

                    <ul x-show="open" @click.away="open = false"
                        class="absolute left-0 right-0 mt-1 bg-white border rounded shadow z-10" style="display: none;">
                        <li class="px-3 py-1 text-black hover:bg-gray-100 cursor-pointer"
                            @click="selected = 'Last day'; open = false">Last day</li>
                        <li class="px-3 py-1 text-black hover:bg-gray-100 cursor-pointer"
                            @click="selected = 'Last 7 days'; open = false">Last 7 days</li>
                        <li class="px-3 py-1 text-black hover:bg-gray-100 cursor-pointer"
                            @click="selected = 'Last 30 days'; open = false">Last 30 days</li>
                        <li class="px-3 py-1 text-black hover:bg-gray-100 cursor-pointer"
                            @click="selected = 'Last month'; open = false">Last month</li>
                        <li class="px-3 py-1 text-black hover:bg-gray-100 cursor-pointer"
                            @click="selected = 'Last year'; open = false">Last year</li>
                    </ul>
                </div>

                <!-- Search -->
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <span class="material-symbols-outlined text-black text-lg">search</span>
                    </div>
                    <input type="text" id="table-search"
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-white focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search for employee">
                </div>
            </div>