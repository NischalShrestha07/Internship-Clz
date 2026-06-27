<div class="main transition-all duration-400">
    <!-- Header Box -->
    <x-notifications />

    @if ($showForm)

    <x-headings title="Manage Employee Details" />
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
        <div class="mb-6 p-6 border border-gray-200 dark:border-gray-700 rounded-lg d-flex justify-between ">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white ">
                    {{ $editMode ? 'Edit Employee' : 'Add New Employee' }}
                </h3>
                <button wire:click="back"
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-800 dark:hover:bg-blue-700 text-white px-2 py-1 rounded-md">
                    Back
                </button>
            </div>
            <hr class="w-full border-gray-300 dark:border-gray-600 mt-1 mb-5" />
            <x-form submitAction="saveEmployee" submitText="{{ $editMode ? 'Update Employee' : 'Add Employee' }}"
                enctype="multipart/form-data">
                <div class="mb-8">
                    <div x-data="{ photoPreview: null }" class="flex items-center gap-6 mb-3">
                        <div class="flex items-center gap-4">
                            <label class="relative cursor-pointer group block w-full aspect-square max-w-sm">

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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Full
                                Name</label>
                            <input type="text" wire:model="name" placeholder="Name" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-4 py-3
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                            <input type="email" wire:model="email" placeholder="Email" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (!$editMode)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date of
                                Birth</label>
                            <input type="date" wire:model="date_of_birth" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 date-input
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('date_of_birth')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Role</label>
                            <select wire:model="role" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm select-icon
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Position</label>
                            <input type="text" wire:model="position" placeholder="Position" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('position')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Address</label>
                            <input type="text" wire:model="address" placeholder="Address" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('address')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
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

                        @if (!$editMode)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
                            <input type="password" wire:model="password" placeholder="Password" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Gender</label>
                            <select wire:model="gender" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm select-icon
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Joined
                                Date</label>
                            <input type="date" wire:model="joined_date" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 date-input
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('joined_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Basic
                                Salary</label>
                            <input type="number" wire:model="basic_salary" placeholder="Salary" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                                                text-gray-900 dark:text-gray-100
                                                bg-white dark:bg-gray-800
                                                focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('basic_salary')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="space-y-4 md:col-span-2">
                        <h4 class="text-md font-medium text-gray-800 dark:text-white border-b pb-2">Documents</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                            {{-- Citizenship Front --}}
                            <div x-data="{ frontPreview: null }">
                                <label for="citizenship_front"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Citizenship Front <span class="text-xs text-gray-500 dark:text-gray-400">(Image
                                        only)</span>
                                </label>

                                <div
                                    class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                                    <input type="file" id="citizenship_front" wire:model="citizenship_front"
                                        accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        x-ref="frontFile"
                                        @change="const file = $refs.frontFile.files[0]; if (file) { frontPreview = URL.createObjectURL(file) }" />
                                    <div
                                        class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
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
                                            <img :src="frontPreview" alt="Citizenship Front Preview"
                                                class="h-50 w-full object-cover border dark:border-gray-600 rounded" />
                                            <button type="button"
                                                @click="frontPreview = null; $refs.frontFile.value = ''; $wire.set('citizenship_front', null)"
                                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">
                                                ×
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="!frontPreview">
                                        <div class="mt-4 relative">
                                            @if ($editMode)
                                            @if ($citizenship_front)
                                            <div class="relative">
                                                <img src="{{ asset('storage/' . $citizenship_front) }}"
                                                    alt="Citizenship Front"
                                                    class="h-50 w-full object-cover border dark:border-gray-600 rounded">
                                                <button type="button" wire:click="clearCitizenshipFront"
                                                    class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm z-10">
                                                    ×
                                                </button>
                                            </div>
                                            @else
                                            <p class="text-sm text-gray-500 dark:text-gray-300 mt-2">No
                                                front photo uploaded yet.</p>
                                            @endif
                                            @else
                                            {{-- show-only mode: only display if exists, no fallback --}}
                                            @if ($citizenship_front)
                                            <div class="relative">
                                                <img src="{{ asset('storage/' . $citizenship_front) }}"
                                                    alt="Citizenship Front"
                                                    class="h-50 w-full object-cover border dark:border-gray-600 rounded">
                                            </div>
                                            @endif
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </div>


                            {{-- Citizenship Back --}}
                            <div x-data="{
                                                backPreview: null,
                                                init() {
                                                    this.$watch('backPreview', value => {
                                                        if (!value) {
                                                            // ensure Livewire side is cleared if preview is removed
                                                            $wire.set('citizenship_back', null);
                                                        }
                                                    });
                                                },
                                                setPreview(file) {
                                                    if (!file) return;
                                                    // revoke old if exists
                                                    if (this.backPreview) {
                                                        URL.revokeObjectURL(this.backPreview);
                                                    }
                                                    this.backPreview = URL.createObjectURL(file);
                                                },
                                                clearPreview() {
                                                    if (this.backPreview) {
                                                        URL.revokeObjectURL(this.backPreview);
                                                    }
                                                    this.backPreview = null;
                                                    $refs.backFile.value = '';
                                                    $wire.set('citizenship_back', null);
                                                }
                                            }">
                                <label for="citizenship_back"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Citizenship Back <span class="text-xs text-gray-500 dark:text-gray-400">(Image
                                        only)</span>
                                </label>

                                <div
                                    class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                                    <input type="file" id="citizenship_back" wire:model="citizenship_back"
                                        accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        x-ref="backFile" @change="setPreview($refs.backFile.files[0])" />
                                    <div
                                        class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
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
                                            <img :src="backPreview" alt="Citizenship Back Preview"
                                                class="h-[200px] w-full object-cover border dark:border-gray-600 rounded" />
                                            <button type="button" @click="clearPreview()"
                                                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">
                                                ×
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="!backPreview">
                                        <div class="mt-4 relative">
                                            @if ($editMode)
                                            @if ($citizenship_back)
                                            <div class="relative" wire:key="citizenship-back-{{ $citizenship_back }}">
                                                <img src="{{ asset('storage/' . $citizenship_back) }}"
                                                    alt="Citizenship Back"
                                                    class="h-[200px] w-full object-cover border dark:border-gray-600 rounded">
                                                <button type="button" wire:click="clearCitizenshipBack"
                                                    class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm z-10">
                                                    ×
                                                </button>
                                            </div>
                                            @else
                                            <p class="text-sm text-gray-500 dark:text-gray-300 mt-2">No
                                                back photo uploaded yet.</p>
                                            @endif
                                            @else
                                            {{-- show-only mode --}}
                                            @if ($citizenship_back)
                                            <div class="relative" wire:key="citizenship-back-{{ $citizenship_back }}">
                                                <img src="{{ asset('storage/' . $citizenship_back) }}"
                                                    alt="Citizenship Back"
                                                    class="h-[200px] w-full object-cover border dark:border-gray-600 rounded">
                                            </div>
                                            @endif
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </div>


                            {{-- CV Upload --}}
                            <div x-data="{ cvPreview: null }">
                                <label for="cv" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Curriculum Vitae (CV) <span class="text-xs text-gray-500 dark:text-gray-400">(PDF or
                                        Image)</span>
                                </label>
                                <!-- Upload Area -->
                                <div
                                    class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                                    <input type="file" id="cv" wire:model="CV_file" accept="application/pdf,image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-ref="cvFile"
                                        @change="
                                                            const file = $refs.cvFile.files[0];
                                                            if (file) {
                                                                const reader = new FileReader();
                                                                reader.onload = e => cvPreview = e.target.result;
                                                                reader.readAsDataURL(file);
                                                            } else {
                                                                cvPreview = null;
                                                            }
                                                        " />
                                    <div
                                        class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                                        <span class="material-symbols-outlined text-3xl">upload</span>
                                        <span class="text-sm">Upload CV</span>
                                    </div>
                                </div>
                                @error('CV_file')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                                @enderror
                                <!-- Live Preview -->
                                <div class="mt-4">
                                    <!-- PDF Preview -->
                                    <template x-if="cvPreview && cvPreview.includes('application/pdf')">
                                        <div
                                            class="relative w-full h-full border rounded dark:border-gray-600 overflow-auto">
                                            <iframe :src="cvPreview" class="w-full h-50 rounded"
                                                frameborder="0"></iframe>
                                            <button type="button" wire:click="clearCv"
                                                @click="cvPreview = null; $refs.cvFile.value = ''"
                                                class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 flex items-center justify-center rounded-full text-sm z-10">
                                                ×
                                            </button>
                                        </div>
                                    </template>
                                    <!-- Image Preview -->
                                    <template x-if="cvPreview && !cvPreview.includes('application/pdf')">
                                        <div class="relative">
                                            <img :src="cvPreview" alt="CV Preview"
                                                class="w-full h-50 object-cover border rounded dark:border-gray-600">
                                            <button type="button" wire:click="clearCv"
                                                @click="cvPreview = null; $refs.cvFile.value = ''"
                                                class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 flex items-center justify-center rounded-full text-sm z-10">
                                                ×
                                            </button>
                                        </div>
                                    </template>
                                    <!-- Stored File Fallback -->
                                    <template x-if="!cvPreview">
                                        <div class="mt-4 relative">
                                            <div class="mt-4 relative">
                                                @if ($editMode)
                                                @if ($CV_file)
                                                @if (Str::endsWith($CV_file, ['.png', '.jpg', '.jpeg']))
                                                <img src="{{ asset('storage/' . $CV_file) }}" alt="Saved CV Image"
                                                    class="w-full h-50 object-cover border rounded dark:border-gray-600">
                                                @elseif (Str::endsWith($CV_file, '.pdf'))
                                                <iframe src="{{ asset('storage/' . $CV_file) }}"
                                                    class="w-full h-50 border rounded dark:border-gray-600"
                                                    frameborder="0"></iframe>
                                                @else
                                                <p class="text-sm text-gray-500 dark:text-gray-300">
                                                    Unsupported file format.</p>
                                                @endif
                                                <button type="button" wire:click="clearCv"
                                                    @click="$wire.set('CV_file', null)"
                                                    class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm z-10">
                                                    ×
                                                </button>
                                                @else
                                                <p class="text-sm text-gray-500 dark:text-gray-300">No CV
                                                    uploaded yet.</p>
                                                @endif
                                                @endif
                                            </div>

                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
    @endif
    @if (!$showForm)
    <x-headings title="Manage Employee Details" buttonText2="+ Add New Employee" buttonAction="addEmployee" />

    <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="overflow-x-auto rounded-lg">
            <x-table title="Manage Employee" :search="true">
                <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-4 font-semibold">Employee Image</th>
                        <th class="px-4 py-4 font-semibold">Name</th>
                        <th class="px-4 py-4 font-semibold">Email</th>
                        <th class="px-4 py-4 font-semibold">Position</th>
                        <th class="px-4 py-4 font-semibold">Joined Date</th>
                        <th class="px-4 py-4 font-semibold">Role</th>
                        <th class="px-4 py-4 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @forelse($employees as $employee)
                    <tr>
                        <td class="px-4 py-3 flex justify-center items-center">
                            @if ($employee->employee && $employee->employee->image)
                            <img src="{{ asset('storage/' . $employee->employee->image) }}" alt="Employee Image"
                                class="w-16 h-16 object-cover border border-gray-300 rounded-md">
                            @else
                            <img src="{{ asset('default.jpg') }}" alt="Default Image"
                                class="w-16 h-16 object-cover border border-gray-300 rounded-md">
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            {{ $employee->name }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $employee->email }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $employee->employee->position ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ optional($employee->employee)->joined_date
                            ? \Carbon\Carbon::parse($employee->employee->joined_date)->format('M d, Y')
                            : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $role = $employee->getRoleNames()->first();
                            $roleColors = [
                            'Admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                            'HR Manager' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100',
                            'Employee' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100',
                            ];
                            $color = $roleColors[$role] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700
                            dark:text-gray-300';
                            @endphp
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ $role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <div>
                                        <button @click="open = !open" type="button"
                                            class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                            id="menu-button" aria-expanded="true" aria-haspopup="true">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition-colors"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Dropup menu -->
                                    <div x-show="open" @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="origin-bottom-right absolute right-0 bottom-full mb-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                                        tabindex="-1">
                                        <div class="py-1" role="none">
                                            <!-- Edit -->
                                            <button wire:click="editEmployee({{ $employee->id }})" @click="open = false"
                                                class="flex items-center px-3 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>

                                            <!-- View -->
                                            <button wire:click="viewEmployee({{ $employee->id }})" @click="open = false"
                                                class="flex items-center px-3 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </button>

                                            <!-- Attendance Report -->
                                            <button wire:click="showAttendanceReport({{ $employee->id }})"
                                                @click="open = false"
                                                class="flex items-center px-3 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                Attendance Report
                                            </button>

                                            <!-- Leave Report -->
                                            <button wire:click="showLeaveReport({{ $employee->id }})"
                                                @click="open = false"
                                                class="flex items-center px-3 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Leave Report
                                            </button>

                                            <!-- Delete -->
                                            <button wire:click="confirmDelete({{ $employee->id }})"
                                                @click="open = false"
                                                class="flex items-center px-3 py-0 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 w-full text-left transition-colors duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <select wire:model.live="userPerPage"
                    class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                    <option value="{{ $defaultPerPage }}">Default ({{ $defaultPerPage }})</option>
                </select>
                @if($userPerPage != $defaultPerPage)
                <button wire:click="resetToDefaultPerPage"
                    class="bg-blue-600 dark:bg-blue-800 text- ml-3 px-3 py-1 rounded hover:bg-blue-700">
                    Reset </button>
                @endif
            </div>

            <div>
                {{ $employees->links() }}
            </div>
        </div>
    </div>
    @endif
    <!-- Delete Confirmation Modal -->
    <x-modal name="cancel-leave" :show="$errors->isNotEmpty()" focusable>
        <!-- Existing delete modal code remains the same -->
        <div class="fixed inset-0  bg-opacity-40 flex items-center justify-center z-50">

            <div
                class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[500px] items-center p-6 relative mx-2">
                <!-- Close Button -->
                <button x-on:click.prevent="$dispatch('close')"
                    class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">
                    &times;
                </button>

                <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white ">Delete Employee</h2>

                <p class="text-center text-gray-700 dark:text-white  text-lg mb-6">
                    Are you sure you want to delete this employee?
                </p>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="confirmDeleteEmployee"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600">
                        Delete
                    </button>

                    <button type="button" x-on:click.prevent="$dispatch('close')"
                        class="px-4 py-2 text-gray-600 dark:text-gray-300  hover:text-black dark:hover:text-white ">
                        Back
                    </button>
                </div>
            </div>
        </div>
    </x-modal>
    <x-modal name="view-employee">
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] relative flex flex-col">

                <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 shadow-sm">
                    <!-- Close Button -->
                    <button x-on:click.prevent="$dispatch('close')"
                        class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center transition">
                        &times;
                    </button>
                    <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white">Employee Details</h2>

                    <hr class="border-t border-gray-200 dark:border-gray-700 mt-4" />
                </div>

                <div class="overflow-y-auto px-6 py-6 space-y-6">

                    <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                        <!-- Profile Picture -->
                        <div x-data="{ previewOpen: false }" class="relative group inline-block">
                            <!-- Profile Picture with click to open preview -->
                            <div class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow overflow-hidden cursor-pointer relative"
                                @click="previewOpen = true" title="Click to preview larger">
                                <img src="{{ $imagePreview ? asset('storage/' . $imagePreview) : asset('default.jpg') }}"
                                    alt="Profile Picture" class="w-full h-full object-cover" />
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                    <div class="text-white text-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-base">zoom_in</span>
                                        <span>Preview</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Modal -->
                            <div x-show="previewOpen" x-transition @click.away="previewOpen = false"
                                class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">

                                <div class="relative">
                                    <!-- Close Button -->
                                    <button @click="previewOpen = false"
                                        class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">×</button>

                                    <img src="{{ $imagePreview ? asset('storage/' . $imagePreview) : asset('default.jpg') }}"
                                        alt="Profile Picture Preview"
                                        class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-lg" @click.stop />
                                </div>
                            </div>
                        </div>

                        <!-- Basic Info -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ $name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Position</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ $position }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</p>
                                <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $phone_number }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</p>
                                <p class="text-gray-900 dark:text-white mt-1">{{ $date_of_birth }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</p>
                                <p class="text-gray-900 dark:text-white mt-1">{{ $gender }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</p>
                                <p class="text-gray-900 dark:text-white mt-1">{{ $address }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Joined Date</p>
                                <p class="text-gray-900 dark:text-white mt-1">{{ $joined_date }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</p>
                                <p class="text-gray-900 dark:text-white mt-1">{{ $role }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Documents</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Citizenship Front -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Citizenship Front
                                    </p>
                                </div>
                                <div class="h-48 flex items-center justify-center p-4 text-center">
                                    @if ($citizenship_front && str_contains($citizenship_front, '.pdf'))
                                    <p class="text-sm text-gray-500 dark:text-gray-400"> PDF Document</p>
                                    @elseif ($citizenship_front)
                                    <img src="{{ asset('storage/' . $citizenship_front) }}"
                                        class="max-h-full max-w-full object-contain" alt="Citizenship Front">
                                    @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No document</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Citizenship Back -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Citizenship Back
                                    </p>
                                </div>
                                <div class="h-48 flex items-center justify-center p-4 text-center">
                                    @if ($citizenship_back && str_contains($citizenship_back, '.pdf'))
                                    <p class="text-sm text-gray-500 dark:text-gray-400">PDF Document</p>
                                    @elseif ($citizenship_back)
                                    <img src="{{ asset('storage/' . $citizenship_back) }}"
                                        class="max-h-full max-w-full object-contain" alt="Citizenship Back">
                                    @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No document</p>
                                    @endif
                                </div>
                            </div>

                            <!-- CV/Resume -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">CV/Resume</p>
                                </div>
                                <div class="h-48 flex items-center justify-center p-4 text-center">
                                    @if ($CV_file && str_contains($CV_file, '.pdf'))
                                    <iframe src="{{ asset('storage/' . $CV_file) }}" class="w-full h-full rounded"
                                        frameborder="0"></iframe>
                                    @elseif ($CV_file)
                                    <img src="{{ asset('storage/' . $CV_file) }}"
                                        class="max-h-full max-w-full object-contain rounded" alt="CV">
                                    @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No document</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- Attendance Report Modal -->
    <x-modal name="attendance-report">
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-6xl max-h-[90vh] relative flex flex-col">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Attendance Report</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $selectedEmployee->name ?? '' }} • {{ now()->format('F Y') }}
                            </p>
                        </div>
                        <button x-on:click.prevent="$dispatch('close')"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div wire:loading class="flex-1 flex items-center justify-center p-8">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4">
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">Loading attendance data...</p>
                    </div>
                </div>

                <!-- Content -->
                <div wire:loading.remove class="flex-1 overflow-y-auto">
                    @if($attendanceReport)
                    <div class="space-y-6 p-6">
                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Present -->
                            <div
                                class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-900/30">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-green-600 dark:text-green-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-green-700 dark:text-green-300">Present Days
                                        </p>
                                        <p class="text-2xl font-semibold text-green-900 dark:text-green-100 mt-1">
                                            {{ $attendanceSummary['present'] ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Absent -->
                            <div
                                class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-100 dark:border-red-900/30">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-red-600 dark:text-red-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-red-700 dark:text-red-300">Absent Days</p>
                                        <p class="text-2xl font-semibold text-red-900 dark:text-red-100 mt-1">
                                            {{ $attendanceSummary['absent'] ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Hours -->
                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-900/30">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Hours</p>
                                        <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100 mt-1">
                                            {{ number_format($attendanceSummary['total_hours'] ?? 0, 1) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance History Table -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Check In
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Check Out
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Hours Worked
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($attendanceReport as $record)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($record->check_in)->format('M d, Y') }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($record->check_in)->format('l') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                $statusColors = [
                                                'Checked-Out' => 'bg-green-100 text-green-800 dark:bg-green-900
                                                dark:text-green-100',
                                                'working' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900
                                                dark:text-yellow-100',
                                                'absent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                                                ];
                                                $status = $record->check_out ? 'Checked-Out' : 'working';
                                                @endphp
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                    {{ $record->check_out ? 'Checked-Out' : 'Working' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ $record->check_out ?
                                                    \Carbon\Carbon::parse($record->check_out)->format('h:i A') : '--' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ $record->check_out ?
                                                    number_format(\Carbon\Carbon::parse($record->check_in)->floatDiffInHours(\Carbon\Carbon::parse($record->check_out)),
                                                    2) . ' hrs' :
                                                    '--' }}
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5"
                                                class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No attendance records found for this period.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500 mb-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No attendance data available
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">There are no attendance records for the selected
                            period.
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div
                    class="sticky bottom-0 bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <button x-on:click.prevent="$dispatch('close')"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- Leave Report Modal -->
    <x-modal name="leave-report">
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-6xl max-h-[90vh] relative flex flex-col">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Leave Report</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $selectedEmployee->name ?? '' }} • {{ now()->format('F Y') }}
                            </p>
                        </div>
                        <button x-on:click.prevent="$dispatch('close')"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div wire:loading class="flex-1 flex items-center justify-center p-8">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4">
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">Loading leave data...</p>
                    </div>
                </div>

                <!-- Content -->
                <div wire:loading.remove class="flex-1 overflow-y-auto">
                    @if($leaveReport)
                    <div class="space-y-6 p-6">
                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Approved -->
                            <div
                                class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-900/30">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-green-600 dark:text-green-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-green-700 dark:text-green-300">Approved</p>
                                        <p class="text-2xl font-semibold text-green-900 dark:text-green-100 mt-1">
                                            {{ $leaveSummary['approved'] ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Rejected -->
                            <div
                                class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-100 dark:border-red-900/30">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-red-600 dark:text-red-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-red-700 dark:text-red-300">Rejected</p>
                                        <p class="text-2xl font-semibold text-red-900 dark:text-red-100 mt-1">
                                            {{ $leaveSummary['rejected'] ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending -->
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-100 dark:border-yellow-900/30">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Pending</p>
                                        <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100 mt-1">
                                            {{ $leaveSummary['pending'] ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Days -->
                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-900/30">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Days</p>
                                        <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100 mt-1">
                                            {{ number_format($leaveSummary['total_days'] ?? 0, 1) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Leave History Table -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Leave Type
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date Range
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Days
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Reason
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($leaveReport as $leave)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $leave->leaveType->leave_type ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $leave->leaveType->default_days ?? 0 }} days allowed
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y')
                                                    }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($leave->start_date_time)->format('h:i A')
                                                    }} -
                                                    {{ \Carbon\Carbon::parse($leave->end_date_time)->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100">
                                                    {{ $leave->leaveType->default_days }} day(s)
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                $statusColors = [
                                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900
                                                dark:text-green-100',
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900
                                                dark:text-yellow-100',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900
                                                dark:text-red-100',
                                                'declined' => 'bg-red-100 text-red-800 dark:bg-red-900
                                                dark:text-red-100',
                                                ];
                                                @endphp
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[strtolower($leave->status)] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                    {{ ucfirst($leave->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                                    {{ $leave->leave_reason }}
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5"
                                                class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                No leave records found for this period.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500 mb-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No leave data available</h3>
                        <p class="text-gray-500 dark:text-gray-400">There are no leave records for the selected period.
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div
                    class="sticky bottom-0 bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <button x-on:click.prevent="$dispatch('close')"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </x-modal>
</div>