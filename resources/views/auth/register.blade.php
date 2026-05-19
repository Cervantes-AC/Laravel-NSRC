<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" :value="__('Username')" />
                <span class="text-red-500">*</span>
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="full_name" :value="__('Full Name')" />
                <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name')" />
                <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <span class="text-red-500">*</span>
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="school_id" :value="__('School ID')" />
                <x-text-input id="school_id" class="block mt-1 w-full" type="text" name="school_id" :value="old('school_id')" />
                <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <span class="text-red-500">*</span>
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <span class="text-red-500">*</span>
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Select Gender') }}</option>
                    <option value="male" @selected(old('gender') === 'male')>{{ __('Male') }}</option>
                    <option value="female" @selected(old('gender') === 'female')>{{ __('Female') }}</option>
                    <option value="other" @selected(old('gender') === 'other')>{{ __('Other') }}</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="personal_contact_number" :value="__('Contact Number')" />
                <x-text-input id="personal_contact_number" class="block mt-1 w-full" type="text" name="personal_contact_number" :value="old('personal_contact_number')" placeholder="09XX-XXX-XXXX" />
                <x-input-error :messages="$errors->get('personal_contact_number')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="college" :value="__('College')" />
                <x-text-input id="college" class="block mt-1 w-full" type="text" name="college" :value="old('college')" />
                <x-input-error :messages="$errors->get('college')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="major" :value="__('Major')" />
                <x-text-input id="major" class="block mt-1 w-full" type="text" name="major" :value="old('major')" />
                <x-input-error :messages="$errors->get('major')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="year_level" :value="__('Year Level')" />
                <select id="year_level" name="year_level" class="block mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Select Year') }}</option>
                    <option value="1st Year" @selected(old('year_level') === '1st Year')>{{ __('1st Year') }}</option>
                    <option value="2nd Year" @selected(old('year_level') === '2nd Year')>{{ __('2nd Year') }}</option>
                    <option value="3rd Year" @selected(old('year_level') === '3rd Year')>{{ __('3rd Year') }}</option>
                    <option value="4th Year" @selected(old('year_level') === '4th Year')>{{ __('4th Year') }}</option>
                </select>
                <x-input-error :messages="$errors->get('year_level')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="terms" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('terms') ? 'checked' : '' }}>
                <span class="ms-2 text-sm text-gray-600">
                    {{ __('I agree to the') }} <a href="#" class="underline text-indigo-600 hover:text-indigo-900">{{ __('Terms and Conditions') }}</a>
                    <span class="text-red-500">*</span>
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
