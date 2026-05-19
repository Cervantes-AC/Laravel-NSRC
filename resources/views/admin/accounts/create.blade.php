<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Create Account') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Username') }} <span class="text-red-500">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('name')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                                <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('full_name')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }} <span class="text-red-500">*</span></label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('email')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="school_id" class="block text-sm font-medium text-gray-700">{{ __('School ID') }}</label>
                                <input id="school_id" name="school_id" type="text" value="{{ old('school_id') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('school_id')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }} <span class="text-red-500">*</span></label>
                                <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('password')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }} <span class="text-red-500">*</span></label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">{{ __('Role') }} <span class="text-red-500">*</span></label>
                                <select id="role" name="role" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>{{ __('Member') }}</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                </select>
                                @error('role')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }} <span class="text-red-500">*</span></label>
                                <select id="status" name="status" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                                @error('status')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="personal_contact_number" class="block text-sm font-medium text-gray-700">{{ __('Contact Number') }}</label>
                                <input id="personal_contact_number" name="personal_contact_number" type="text" value="{{ old('personal_contact_number') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('personal_contact_number')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">{{ __('Gender') }}</label>
                                <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" @selected(old('gender') === 'male')>{{ __('Male') }}</option>
                                    <option value="female" @selected(old('gender') === 'female')>{{ __('Female') }}</option>
                                    <option value="other" @selected(old('gender') === 'other')>{{ __('Other') }}</option>
                                </select>
                                @error('gender')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="college" class="block text-sm font-medium text-gray-700">{{ __('College') }}</label>
                                <input id="college" name="college" type="text" value="{{ old('college') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('college')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="major" class="block text-sm font-medium text-gray-700">{{ __('Major') }}</label>
                                <input id="major" name="major" type="text" value="{{ old('major') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('major')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="year_level" class="block text-sm font-medium text-gray-700">{{ __('Year Level') }}</label>
                                <select id="year_level" name="year_level" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Select Year') }}</option>
                                    <option value="1st Year" @selected(old('year_level') === '1st Year')>{{ __('1st Year') }}</option>
                                    <option value="2nd Year" @selected(old('year_level') === '2nd Year')>{{ __('2nd Year') }}</option>
                                    <option value="3rd Year" @selected(old('year_level') === '3rd Year')>{{ __('3rd Year') }}</option>
                                    <option value="4th Year" @selected(old('year_level') === '4th Year')>{{ __('4th Year') }}</option>
                                </select>
                                @error('year_level')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="current_address" class="block text-sm font-medium text-gray-700">{{ __('Current Address') }}</label>
                                <input id="current_address" name="current_address" type="text" value="{{ old('current_address') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('current_address')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="home_address" class="block text-sm font-medium text-gray-700">{{ __('Home Address') }}</label>
                                <input id="home_address" name="home_address" type="text" value="{{ old('home_address') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('home_address')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.accounts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                                {{ __('Create Account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
