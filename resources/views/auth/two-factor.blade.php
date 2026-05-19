<x-guest-layout>
    <div x-data="{ method: 'totp' }">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Enter your authentication code to complete sign in.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <input type="hidden" name="method" x-bind:value="method">

            <div class="mb-4">
                <div class="flex gap-2 mb-4">
                    <button type="button" @click="method = 'totp'"
                        class="flex-1 px-3 py-2 text-sm rounded-lg transition"
                        :class="method === 'totp' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                        Authenticator App
                    </button>
                    <button type="button" @click="method = 'email'"
                        class="flex-1 px-3 py-2 text-sm rounded-lg transition"
                        :class="method === 'email' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                        Email Code
                    </button>
                    <button type="button" @click="method = 'backup'"
                        class="flex-1 px-3 py-2 text-sm rounded-lg transition"
                        :class="method === 'backup' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                        Backup Code
                    </button>
                </div>

                <template x-if="method === 'totp'">
                    <p class="text-xs text-gray-500 mb-2">{{ __('Enter the 6-digit code from your authenticator app.') }}</p>
                </template>
                <template x-if="method === 'email'">
                    <p class="text-xs text-gray-500 mb-2">{{ __('A 6-digit code was sent to your email.') }}</p>
                </template>
                <template x-if="method === 'backup'">
                    <p class="text-xs text-gray-500 mb-2">{{ __('Enter one of your recovery codes.') }}</p>
                </template>

                <x-input-label for="code" :value="__('Code')" />
                <x-text-input id="code" class="mt-1 block w-full" type="text" name="code"
                    inputmode="numeric" autocomplete="one-time-code" required autofocus />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div class="mt-4 flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ __('Back to login') }}
                </a>
                <div class="flex gap-2">
                    <template x-if="method === 'email'">
                        <a href="{{ route('two-factor.resend') }}" class="text-sm text-indigo-600 hover:text-indigo-900 underline">
                            {{ __('Resend code') }}
                        </a>
                    </template>
                    <x-primary-button>
                        {{ __('Verify') }}
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
