<div class="flex transition-all duration-400">
    <x-setting-side title=" Settings" />

    <div class="flex-1 px-3 py-2">
        <x-headings title="Profile Settings" />
        <x-notifications />
        <x-form submitAction="update" submitText="Update" enctype="multipart/form-data"
            class="w-full bg-white dark:bg-gray-800 px-4 py-6 rounded-lg shadow-by-force mx-auto">
            <p class="text-gray-700 dark:text-gray-200 col-span-2 text-sm">
                Update your profile below.
            </p>
            <hr class="w-full border-gray-300 dark:border-gray-600 mt-1" />


            {{-- Image --}}
            <div class="mb-8">
                <div x-data="{ photoPreview: null }" class="flex items-center gap-6 mb-3">
                    <div class="flex items-center gap-4">
                        <label class="relative cursor-pointer group block w-20 h-20">
                            <!-- Selected Image Preview -->
                            <template x-if="photoPreview">
                                <div class="relative w-20 h-20">
                                    <img :src="photoPreview" alt="Profile Picture"
                                        class="w-full h-full object-cover border dark:border-gray-600 rounded-full" />
                                    <button type="button" @click="photoPreview = null; $refs.imageInput.value = ''"
                                        class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center leading-none z-10">
                                        ×
                                    </button>
                                </div>
                            </template>

                            <template x-if="!photoPreview">
                                <div class="relative w-20 h-20">
                                    <img src="{{ $imagePreview ? asset('storage/' . $imagePreview) : asset('default.jpg') }}"
                                        alt="Current Profile"
                                        class="w-full h-full object-cover border dark:border-gray-600 rounded-full" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="flex items-center gap-1 text-white text-sm">
                                            <span class="material-symbols-outlined text-base">edit</span>
                                            <span>Edit</span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <input type="file" x-ref="imageInput" wire:model="image" accept="image/*" class="hidden"
                                @change="
                        const file = $event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = e => photoPreview = e.target.result;
                            reader.readAsDataURL(file);
                        } else {
                            photoPreview = null;
                        }
                    " />
                        </label>
                    </div>
                </div>

                @error('image')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Input Fields --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Full Name</label>
                    <input type="text" id="Name" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="name" />
                    @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Phone Number
                    </label>
                    <div class="flex rounded shadow-sm">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l">
                            +977
                        </span>
                        <input type="number" id="phone"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-r px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required wire:model="phone_number" placeholder="98XXXXXXXX" />
                    </div>

                    @error('phone_number')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                    <input type="email" id="email" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="email" />
                    @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Gender</label>
                    <select id="gender" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm select-icon
           text-gray-900 dark:text-gray-100
           bg-white dark:bg-gray-800
           focus:outline-none focus:ring-2 focus:ring-blue-500
           h-10" required wire:model="gender">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date of Birth</label>
                    <input type="date" id="date_of_birth" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 date-input
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="date_of_birth" />
                    @error('date_of_birth')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date of Joining</label>
                    <input type="date" id="joined_date" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 date-input
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="joined_date" />
                    @error('joined_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-5">
                <div>

                    <input type="hidden" id="position" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="position" />

                </div>
                <div>

                    <input type="hidden" id="basic_salary" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="basic_salary" />

                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Address</label>
                    <input type="text" id="address" placeholder="Address" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="address" />
                    @error('address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Upload Files</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 ml-3">
                    {{-- Citizenship Front --}}
                    <div x-data="{ frontPreview: null }">
                        <label for="citizenship_front"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Citizenship front <span class="text-xs text-gray-500 dark:text-gray-400">(Image
                                only)</span>
                        </label>

                        <div x-show="!frontPreview && !@js($citizenship_front_preview)"
                            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                            <input type="file" id="citizenship_front" wire:model="citizenship_front" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-ref="frontFile"
                                @change="const file = $refs.frontFile.files[0]; if (file) { frontPreview = URL.createObjectURL(file) }" />
                            <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                                <span class="material-symbols-outlined text-3xl">upload</span>
                                <span class="text-sm">Upload</span>
                            </div>
                        </div>

                        @error('citizenship_front')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <div class="mt-4">
                            <template x-if="frontPreview">
                                <div class="relative w-full h-full">
                                    <img :src="frontPreview" alt="Citizenship front Preview"
                                        class="h-50 w-full object-cover border dark:border-gray-600 rounded" />
                                    <button type="button"
                                        @click="frontPreview = null; $refs.frontFile.value = ''; $wire.set('citizenship_front', null)"
                                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">×</button>
                                </div>
                            </template>
                            <template x-if="!frontPreview">
                                @if ($citizenship_front_preview)
                                <div class="mt-4 w-full h-full relative">
                                    <img src="{{ asset('storage/' . $citizenship_front_preview) }}"
                                        alt="Citizenship front"
                                        class="h-50 w-full object-cover border dark:border-gray-600 rounded">
                                    <button type="button" wire:click="clearCitizenshipFrontPreviewOnly"
                                        @click="$wire.set('citizenship_front', null)"
                                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">×</button>
                                </div>
                                @else
                                <p class="text-sm text-gray-500 dark:text-gray-300 mt-2">No front photo uploaded
                                    yet.</p>
                                @endif
                            </template>
                        </div>

                    </div>

                    {{-- Citizenship Back --}}
                    <div x-data="{ backPreview: null }">
                        <label for="citizenship_back"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Citizenship back <span class="text-xs text-gray-500 dark:text-gray-400">(Image
                                only)</span>
                        </label>

                        <div x-show="!backPreview && !@js($citizenship_back_preview)"
                            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                            <input type="file" id="citizenship_back" wire:model="citizenship_back" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-ref="backFile"
                                @change="const file = $refs.backFile.files[0]; if (file) { backPreview = URL.createObjectURL(file) }" />
                            <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                                <span class="material-symbols-outlined text-3xl">upload</span>
                                <span class="text-sm">Upload</span>
                            </div>
                        </div>

                        @error('citizenship_back')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <div class="mt-4">
                            <template x-if="backPreview">
                                <div class="relative w-full h-full">
                                    <img :src="backPreview" alt="Citizenship back Preview"
                                        class="h-50 w-full object-cover border dark:border-gray-600 rounded" />
                                    <button type="button"
                                        @click="backPreview = null; $refs.backFile.value = ''; $wire.set('citizenship_back', null)"
                                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">×</button>
                                </div>
                            </template>
                            <template x-if="!backPreview">
                                @if ($citizenship_back_preview)
                                <div class="mt-4 w-full h-full relative">
                                    <img src="{{ asset('storage/' . $citizenship_back_preview) }}"
                                        alt="Citizenship back"
                                        class="h-50 w-full object-cover border dark:border-gray-600 rounded">
                                    <button type="button" wire:click="clearCitizenshipBackPreviewOnly"
                                        @click="$wire.set('citizenship_back', null)"
                                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">×</button>
                                </div>
                                @else
                                <p class="text-sm text-gray-500 dark:text-gray-300 mt-2">No back photo uploaded
                                    yet.</p>
                                @endif
                            </template>
                        </div>

                    </div>

                    {{-- CV Upload --}}
                    <div x-data="{ cvPreview: null }">
                        <label for="cv" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Curriculum Vitae (CV)
                            <span class="text-xs text-gray-500 dark:text-gray-400">(PDF or Image)</span>
                        </label>

                        <!-- Upload Input -->
                        <div x-show="!cvPreview && !@js($CV_file_preview)"
                            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                            <input type="file" id="cv" wire:model="CV_file" accept="application/pdf,image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-ref="cvFile" @change="const file = $refs.cvFile.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => cvPreview = e.target.result;
                    reader.readAsDataURL(file);
                } else {
                    cvPreview = null;
                }" />
                            <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                                <span class="material-symbols-outlined text-3xl">upload</span>
                                <span class="text-sm">Upload CV</span>
                            </div>
                        </div>

                        @error('CV_file')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror

                        <!-- Preview Section -->
                        <div class="mt-4">
                            <!-- PDF Preview -->
                            <template x-if="cvPreview && cvPreview.includes('data:application/pdf')">
                                <div class="relative w-full border rounded dark:border-gray-600 overflow-auto">
                                    <iframe :src="cvPreview" class="w-full h-50 rounded" frameborder="0"></iframe>
                                    <button type="button"
                                        @click="cvPreview = null; $refs.cvFile.value = ''; $wire.set('CV_file', null)"
                                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs z-10">
                                        ×
                                    </button>
                                </div>
                            </template>

                            <!-- Image Preview -->
                            <template
                                x-if="cvPreview && (cvPreview.includes('data:image/png') || cvPreview.includes('data:image/jpeg') || cvPreview.includes('data:image/jpg'))">
                                <div class="relative w-full border rounded dark:border-gray-600">
                                    <img :src="cvPreview" alt="CV Image Preview"
                                        class="w-full h-50 object-contain rounded" />
                                    <button type="button"
                                        @click="cvPreview = null; $refs.cvFile.value = ''; $wire.set('CV_file', null)"
                                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs z-10">
                                        ×
                                    </button>
                                </div>
                            </template>

                            <!-- Stored Preview (Livewire Storage Fallback) -->
                            <template x-if="!cvPreview">
                                <div class="relative mt-4">
                                    @if ($CV_file_preview)
                                    @if (Str::endsWith($CV_file_preview, ['.png', '.jpg', '.jpeg']))
                                    <img src="{{ asset('storage/' . $CV_file_preview) }}" alt="Saved CV Image"
                                        class="w-full h-auto object-cover border rounded dark:border-gray-600">
                                    @elseif (Str::endsWith($CV_file_preview, '.pdf'))
                                    <iframe src="{{ asset('storage/' . $CV_file_preview) }}"
                                        class="w-full h-60 border rounded dark:border-gray-600"
                                        frameborder="0"></iframe>
                                    @else
                                    <p class="text-sm text-gray-500 dark:text-gray-300">Unsupported file
                                        format.</p>
                                    @endif

                                    <button type="button" wire:click="clearCvPreviewOnly"
                                        @click="$wire.set('CV_file', null)"
                                        class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm z-10">
                                        ×
                                    </button>
                                    @else
                                    <p class="text-sm text-gray-500 dark:text-gray-300">No CV uploaded yet.</p>
                                    @endif
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </x-form>
    </div>