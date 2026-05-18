<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Enter the 6-digit verification code sent to your email address.') }}
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Verification Code')" />
            <x-text-input id="code" class="mt-1 block w-full" type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
