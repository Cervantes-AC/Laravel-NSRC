<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Personnel') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.personnel.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                                <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $user->full_name) }}" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                                <input id="password" name="password" type="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-describedby="password-help" />
                                <p id="password-help" class="mt-1 text-xs text-gray-500">{{ __('Leave blank to keep current password.') }}</p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="school_id" class="block text-sm font-medium text-gray-700">{{ __('School ID') }}</label>
                                <input id="school_id" name="school_id" type="text" value="{{ old('school_id', $user->school_id) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('school_id')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700">{{ __('Department') }}</label>
                                <input id="department" name="department" type="text" value="{{ old('department', $user->department) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('department')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">{{ __('Role') }}</label>
                                <select id="role" name="role" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                    <option value="member" {{ old('role', $user->role) === 'member' ? 'selected' : '' }}>{{ __('Member') }}</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                <select id="status" name="status" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.personnel.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition" aria-label="{{ __('Cancel') }}">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Update Personnel') }}">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
