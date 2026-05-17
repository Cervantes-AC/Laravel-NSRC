<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Duty Session') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.sessions.update', $session) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                                <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $session->user->full_name ?? '') }}" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
                                <input id="date" name="date" type="date" value="{{ old('date', $session->date->format('Y-m-d')) }}" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time_in" class="block text-sm font-medium text-gray-700">{{ __('Time In') }}</label>
                                <input id="time_in" name="time_in" type="time" value="{{ old('time_in', $session->time_in ? $session->time_in->format('H:i') : '') }}" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('time_in')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time_out" class="block text-sm font-medium text-gray-700">{{ __('Time Out') }}</label>
                                <input id="time_out" name="time_out" type="time" value="{{ old('time_out', $session->time_out ? $session->time_out->format('H:i') : '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('time_out')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">{{ __('Location') }}</label>
                                <input id="location" name="location" type="text" value="{{ old('location', $session->location) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sector" class="block text-sm font-medium text-gray-700">{{ __('Sector') }}</label>
                                <select id="sector" name="sector" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Select Sector') }}</option>
                                    <option value="education" {{ old('sector', $session->sector) === 'education' ? 'selected' : '' }}>{{ __('Education') }}</option>
                                    <option value="health" {{ old('sector', $session->sector) === 'health' ? 'selected' : '' }}>{{ __('Health') }}</option>
                                    <option value="environment" {{ old('sector', $session->sector) === 'environment' ? 'selected' : '' }}>{{ __('Environment') }}</option>
                                    <option value="social" {{ old('sector', $session->sector) === 'social' ? 'selected' : '' }}>{{ __('Social Services') }}</option>
                                    <option value="disaster" {{ old('sector', $session->sector) === 'disaster' ? 'selected' : '' }}>{{ __('Disaster Response') }}</option>
                                    <option value="other" {{ old('sector', $session->sector) === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                </select>
                                @error('sector')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.sessions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition" aria-label="{{ __('Cancel') }}">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Update duty session') }}">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
