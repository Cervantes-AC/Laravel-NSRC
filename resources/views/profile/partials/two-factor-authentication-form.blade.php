<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Two-Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Add an extra layer of security to your account.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @if (!$user->two_factor_enabled)
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">
                            {{ __('Two-factor authentication is not enabled.') }}
                        </p>
                        <p class="mt-2 text-sm text-yellow-700">
                            {{ __('Protect your account by enabling two-factor authentication. You will need to provide a code from your authenticator app in addition to your password when logging in.') }}
                        </p>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('profile.enable-2fa') }}" class="space-y-6">
                @csrf

                <div>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('Step 1: Scan this QR code with your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc.)') }}
                    </p>
                    <div class="flex justify-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        {!! $qrCode ?? '<p class="text-gray-500 text-sm">QR Code will be generated</p>' !!}
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('Step 2: Enter the 6-digit code from your authenticator app') }}
                    </p>
                    <x-text-input 
                        id="totp_code" 
                        name="totp_code" 
                        type="text" 
                        placeholder="000000"
                        maxlength="6"
                        inputmode="numeric"
                        class="mt-1 block w-full text-center text-2xl tracking-widest"
                        required 
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('totp_code')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Enable Two-Factor Authentication') }}</x-primary-button>
                </div>
            </form>
        @else
            <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ __('Two-factor authentication is enabled.') }}
                        </p>
                        <p class="mt-2 text-sm text-green-700">
                            {{ __('Your account is protected with two-factor authentication.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-3">{{ __('Backup Codes') }}</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        {{ __('Save these backup codes in a safe place. You can use them to access your account if you lose access to your authenticator app.') }}
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 font-mono text-sm space-y-2">
                        @if ($user->two_factor_backup_codes)
                            @foreach (json_decode($user->two_factor_backup_codes, true) ?? [] as $code)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700">{{ $code }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">{{ __('No backup codes available') }}</p>
                        @endif
                    </div>
                </div>

                <form method="post" action="{{ route('profile.disable-2fa') }}" class="space-y-4">
                    @csrf
                    @method('delete')

                    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm text-red-800">
                            {{ __('Disabling two-factor authentication will make your account less secure.') }}
                        </p>
                    </div>

                    <x-danger-button onclick="return confirm('{{ __('Are you sure you want to disable two-factor authentication?') }}')">
                        {{ __('Disable Two-Factor Authentication') }}
                    </x-danger-button>
                </form>
            </div>
        @endif
    </div>
</section>
