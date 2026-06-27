@extends('components.layouts.app')

@section('content')
<x-notifications />

@if(isset($showForm) && $showForm)
    <x-headings title="Manage Employee Details" />
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
        <div class="mb-6 p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ $editMode ? 'Edit Employee' : 'Add New Employee' }}
                </h3>
                <a href="{{ route('hr.employees') }}"
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-800 dark:hover:bg-blue-700 text-white px-2 py-1 rounded-md">
                    Back
                </a>
            </div>
            <hr class="w-full border-gray-300 dark:border-gray-600 mt-1 mb-5" />

            <form method="POST" action="{{ $editMode ? route('hr.employees.update', $editUser->id) : route('hr.employees.store') }}"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if($editMode)
                    @method('PUT')
                @endif

                <div class="mb-8">
                    <div x-data="{ photoPreview: null }" class="flex items-center gap-6 mb-3">
                        <div class="flex items-center gap-4">
                            <label class="relative cursor-pointer group block w-full aspect-square max-w-sm">
                                <template x-if="photoPreview">
                                    <div class="relative w-20 h-20">
                                        <img :src="photoPreview" alt="Profile Picture"
                                            class="w-full h-full object-cover border dark:border-gray-600 rounded-full" />
                                        <button type="button" @click="photoPreview = null; document.getElementById('imageInput').value = ''; document.getElementById('remove_image').value = '1'"
                                            class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center leading-none z-10">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!photoPreview">
                                    <div class="relative w-20 h-20">
                                        <img src="{{ $editMode && $editUser->employee && $editUser->employee->image ? asset('storage/' . $editUser->employee->image) : asset('default.jpg') }}"
                                            alt="Profile"
                                            class="w-full h-full object-cover border dark:border-gray-600 rounded-full" />
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white text-sm">Edit</span>
                                        </div>
                                    </div>
                                </template>
                                <input id="imageInput" type="file" name="image" accept="image/*" class="hidden"
                                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => photoPreview = e.target.result; reader.readAsDataURL(file); document.getElementById('remove_image').value = '' }" />
                            </label>
                        </div>
                    </div>
                    <input type="hidden" name="remove_image" id="remove_image" value="" />
                    @error('image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Full Name</label>
                            <input type="text" name="name" placeholder="Name" value="{{ old('name', $editMode ? $editUser->name : '') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-4 py-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                            <input type="email" name="email" placeholder="Email" value="{{ old('email', $editMode ? $editUser->email : '') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Role</label>
                            <select name="role"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $editMode ? $editUser->getRoleNames()->first() : '') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Position</label>
                            <input type="text" name="position" placeholder="Position" value="{{ old('position', $editMode && $editUser->employee ? $editUser->employee->position : '') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            @error('position')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Address</label>
                            <input type="text" name="address" placeholder="Address" value="{{ old('address', $editMode && $editUser->employee ? $editUser->employee->address : '') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Phone Number</label>
                            <div class="flex rounded shadow-sm">
                                <span class="inline-flex items-center px-3 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l">+977</span>
                                <input type="text" name="phone_number" placeholder="98XXXXXXXX" value="{{ old('phone_number', $editMode && $editUser->employee ? $editUser->employee->phone_number : '') }}"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-r px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            </div>
                            @error('phone_number')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Gender</label>
                            <select name="gender"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $editMode && $editUser->employee ? $editUser->employee->gender : '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $editMode && $editUser->employee ? $editUser->employee->gender : '') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $editMode && $editUser->employee ? $editUser->employee->gender : '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $editMode && $editUser->employee ? $editUser->employee->date_of_birth : '') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            @error('date_of_birth')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Joined Date</label>
                            <input type="date" name="joined_date" value="{{ old('joined_date', $editMode && $editUser->employee ? $editUser->employee->joined_date : '') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            @error('joined_date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Basic Salary</label>
                            <input type="number" name="basic_salary" placeholder="Salary" value="{{ old('basic_salary', $editMode && $editUser->employee ? $editUser->employee->basic_salary : '') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                            @error('basic_salary')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        @if(!$editMode)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
                            <input type="password" name="password" placeholder="Password" value="{{ old('password') }}"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" {{ $editMode ? '' : 'required' }} />
                            @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">New Password (leave blank to keep current)</label>
                            <input type="password" name="password" placeholder="Leave blank to keep current"
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-4 md:col-span-2">
                    <h4 class="text-md font-medium text-gray-800 dark:text-white border-b pb-2">Documents</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                        <div x-data="{ preview: null }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Citizenship Front <span class="text-xs text-gray-500 dark:text-gray-400">(Image only)</span></label>
                            <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                                <input type="file" name="citizenship_front" accept="image/*,application/pdf"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-ref="fileInput"
                                    @change="const file = $refs.fileInput.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => preview = e.target.result; reader.readAsDataURL(file); document.getElementById('remove_citizenship_front').value = '' }" />
                                <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                                    <span class="material-symbols-outlined text-3xl">upload</span>
                                    <span class="text-sm">Upload</span>
                                </div>
                            </div>
                            <input type="hidden" name="remove_citizenship_front" id="remove_citizenship_front" value="" />
                            @error('citizenship_front')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                            <div class="mt-4">
                                <template x-if="preview">
                                    <div class="relative">
                                        <img :src="preview" alt="Preview" class="h-50 w-full object-cover border dark:border-gray-600 rounded" />
                                        <button type="button" @click="preview = null; $refs.fileInput.value = ''; document.getElementById('remove_citizenship_front').value = '1'"
                                            class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">&times;</button>
                                    </div>
                                </template>
                                <template x-if="!preview">
                                    @if($editMode && $editUser->employee && $editUser->employee->citizenship_front)
                                    <div class="relative mt-4">
                                        <img src="{{ asset('storage/' . $editUser->employee->citizenship_front) }}" alt="Citizenship Front" class="h-50 w-full object-cover border dark:border-gray-600 rounded" />
                                        <button type="button" onclick="document.getElementById('remove_citizenship_front').value = '1'; this.closest('.relative').style.display = 'none'"
                                            class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm z-10">&times;</button>
                                    </div>
                                    @endif
                                </template>
                            </div>
                        </div>
                        <div x-data="{ preview: null }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Citizenship Back <span class="text-xs text-gray-500 dark:text-gray-400">(Image only)</span></label>
                            <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                                <input type="file" name="citizenship_back" accept="image/*,application/pdf"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-ref="backFileInput"
                                    @change="const file = $refs.backFileInput.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => preview = e.target.result; reader.readAsDataURL(file); document.getElementById('remove_citizenship_back').value = '' }" />
                                <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                                    <span class="material-symbols-outlined text-3xl">upload</span>
                                    <span class="text-sm">Upload</span>
                                </div>
                            </div>
                            <input type="hidden" name="remove_citizenship_back" id="remove_citizenship_back" value="" />
                            @error('citizenship_back')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                            <div class="mt-4">
                                <template x-if="preview">
                                    <div class="relative">
                                        <img :src="preview" alt="Preview" class="h-[200px] w-full object-cover border dark:border-gray-600 rounded" />
                                        <button type="button" @click="preview = null; $refs.backFileInput.value = ''; document.getElementById('remove_citizenship_back').value = '1'"
                                            class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">&times;</button>
                                    </div>
                                </template>
                                <template x-if="!preview">
                                    @if($editMode && $editUser->employee && $editUser->employee->citizenship_back)
                                    <div class="relative mt-4">
                                        <img src="{{ asset('storage/' . $editUser->employee->citizenship_back) }}" alt="Citizenship Back" class="h-[200px] w-full object-cover border dark:border-gray-600 rounded" />
                                        <button type="button" onclick="document.getElementById('remove_citizenship_back').value = '1'; this.closest('.relative').style.display = 'none'"
                                            class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm z-10">&times;</button>
                                    </div>
                                    @endif
                                </template>
                            </div>
                        </div>
                        <div x-data="{ cvPreview: null }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Curriculum Vitae (CV) <span class="text-xs text-gray-500 dark:text-gray-400">(PDF or Image)</span></label>
                            <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                                <input type="file" name="CV_file" accept="application/pdf,image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" x-ref="cvFileInput"
                                    @change="const file = $refs.cvFileInput.files[0]; if (file) { const reader = new FileReader(); reader.onload = e => cvPreview = e.target.result; reader.readAsDataURL(file); document.getElementById('remove_CV_file').value = '' }" />
                                <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                                    <span class="material-symbols-outlined text-3xl">upload</span>
                                    <span class="text-sm">Upload CV</span>
                                </div>
                            </div>
                            <input type="hidden" name="remove_CV_file" id="remove_CV_file" value="" />
                            @error('CV_file')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                            <div class="mt-4">
                                <template x-if="cvPreview">
                                    <div class="relative">
                                        <template x-if="cvPreview.includes('application/pdf')">
                                            <iframe :src="cvPreview" class="w-full h-50 rounded" frameborder="0"></iframe>
                                        </template>
                                        <template x-if="!cvPreview.includes('application/pdf')">
                                            <img :src="cvPreview" alt="CV Preview" class="w-full h-50 object-cover border rounded dark:border-gray-600" />
                                        </template>
                                        <button type="button" @click="cvPreview = null; $refs.cvFileInput.value = ''; document.getElementById('remove_CV_file').value = '1'"
                                            class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center z-10">&times;</button>
                                    </div>
                                </template>
                                <template x-if="!cvPreview">
                                    @if($editMode && $editUser->employee && $editUser->employee->CV_file)
                                    <div class="relative mt-4">
                                        @if(Str::endsWith($editUser->employee->CV_file, ['.png', '.jpg', '.jpeg']))
                                        <img src="{{ asset('storage/' . $editUser->employee->CV_file) }}" alt="Saved CV" class="w-full h-50 object-cover border rounded dark:border-gray-600" />
                                        @elseif(Str::endsWith($editUser->employee->CV_file, '.pdf'))
                                        <iframe src="{{ asset('storage/' . $editUser->employee->CV_file) }}" class="w-full h-50 border rounded dark:border-gray-600" frameborder="0"></iframe>
                                        @endif
                                        <button type="button" onclick="document.getElementById('remove_CV_file').value = '1'; this.closest('.relative').style.display = 'none'"
                                            class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm z-10">&times;</button>
                                    </div>
                                    @endif
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-800">
                        {{ $editMode ? 'Update Employee' : 'Add Employee' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@else
    <x-headings title="Manage Employee Details" buttonText2="+ Add New Employee"
        buttonAction="{{ route('hr.employees.create') }}" />

    <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <h1 class="text-xl font-semibold text-black dark:text-white">Employee List</h1>
            <form method="GET" action="{{ route('hr.employees') }}" class="flex items-center gap-3">
                <input type="text" name="search" placeholder="Search by Name or Email" value="{{ request('search') }}"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md w-56" />
                <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700">Search</button>
                @if(request('search'))
                <a href="{{ route('hr.employees') }}" class="bg-gray-500 text-white px-3 py-2 rounded-md text-sm hover:bg-gray-600">Clear</a>
                @endif
            </form>
        </div>
        <hr class="border-gray-800 dark:border-gray-600 mb-4" />

        <div class="overflow-x-auto overflow-y-visible rounded-lg">
            <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)] overflow-y-visible">
                <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
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
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                        @forelse($employees as $employee)
                        <tr>
                            <td class="px-4 py-3 flex justify-center items-center">
                                @if($employee->employee && $employee->employee->image)
                                <img src="{{ asset('storage/' . $employee->employee->image) }}" alt="Employee Image"
                                    class="w-16 h-16 object-cover border border-gray-300 rounded-md" />
                                @else
                                <img src="{{ asset('default.jpg') }}" alt="Default Image"
                                    class="w-16 h-16 object-cover border border-gray-300 rounded-md" />
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $employee->name }}</td>
                            <td class="px-4 py-3">{{ $employee->email }}</td>
                            <td class="px-4 py-3">{{ $employee->employee->position ?? 'N/A' }}</td>
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
                                $color = $roleColors[$role] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $role }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('hr.employees.edit', $employee->id) }}"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-md transition-colors" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <a href="{{ route('hr.employees.show', $employee->id) }}"
                                        class="p-1.5 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 rounded-md transition-colors" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('hr.employees.attendance-report', $employee->id) }}"
                                        class="p-1.5 text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/30 rounded-md transition-colors" title="Attendance Report">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                    </a>
                                    <a href="{{ route('hr.employees.leave-report', $employee->id) }}"
                                        class="p-1.5 text-purple-600 hover:bg-purple-50 dark:text-purple-400 dark:hover:bg-purple-900/30 rounded-md transition-colors" title="Leave Report">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </a>
                                    <button type="button" onclick="confirmDelete({{ $employee->id }})"
                                        class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No employees found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('hr.employees') }}" id="perPageForm">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <select name="per_page" onchange="this.form.submit()"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
                        <option value="5" {{ request('per_page', $perPage) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', $perPage) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', $perPage) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page', $perPage) == 20 ? 'selected' : '' }}>20</option>
                        <option value="25" {{ request('per_page', $perPage) == 25 ? 'selected' : '' }}>25</option>
                    </select>
                </form>
            </div>
            <div class="flex justify-center sm:justify-end">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
@endif

<x-modal name="delete-modal">
    <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[500px] items-center p-6 relative mx-2">
            <button x-on:click.prevent="$dispatch('close')"
                class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">&times;</button>
            <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white">Delete Employee</h2>
            <p class="text-center text-gray-700 dark:text-white text-lg mb-6">Are you sure you want to delete this employee?</p>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600">Delete</button>
                    <button type="button" x-on:click.prevent="$dispatch('close')" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white">Back</button>
                </div>
            </form>
        </div>
    </div>
</x-modal>

@php
$deleteBaseUrl = route('hr.employees.destroy', 0);
@endphp
<script>
function confirmDelete(id) {
    document.getElementById('deleteForm').action = '{{ $deleteBaseUrl }}'.replace(/\/0$/, '/' + id);
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-modal' }));
}
</script>
</script>
@endsection
