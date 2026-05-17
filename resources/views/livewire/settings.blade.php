<div class="space-y-6" aria-label="{{ __('User settings') }}">
    <div class="border-b border-gray-200 mb-6" role="tablist" aria-label="{{ __('Settings tabs') }}">
        <nav class="flex space-x-4">
            <button type="button" class="tab-btn px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600" role="tab" aria-selected="true" data-tab="preferences">
                {{ __('Preferences') }}
            </button>
        </nav>
    </div>

    <div id="tab-preferences" class="tab-panel" role="tabpanel">
        <form wire:submit.prevent="savePreferences" class="space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Display Preferences') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="dark-mode" class="text-sm font-medium text-gray-700">{{ __('Dark Mode') }}</label>
                            <p class="text-xs text-gray-500">{{ __('Toggle dark mode for the interface') }}</p>
                        </div>
                        <button type="button" wire:click="$toggle('darkMode')" role="switch" aria-checked="{{ $darkMode ? 'true' : 'false' }}" aria-label="{{ __('Toggle dark mode') }}" class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $darkMode ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $darkMode ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label for="compact-view" class="text-sm font-medium text-gray-700">{{ __('Compact View') }}</label>
                            <p class="text-xs text-gray-500">{{ __('Use a more compact layout') }}</p>
                        </div>
                        <button type="button" wire:click="$toggle('compactView')" role="switch" aria-checked="{{ $compactView ? 'true' : 'false' }}" aria-label="{{ __('Toggle compact view') }}" class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $compactView ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $compactView ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Notification Preferences') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="email-alerts" class="text-sm font-medium text-gray-700">{{ __('Email Alerts') }}</label>
                            <p class="text-xs text-gray-500">{{ __('Receive notification emails') }}</p>
                        </div>
                        <button type="button" wire:click="$toggle('emailAlerts')" role="switch" aria-checked="{{ $emailAlerts ? 'true' : 'false' }}" aria-label="{{ __('Toggle email alerts') }}" class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $emailAlerts ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $emailAlerts ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label for="sms-alerts" class="text-sm font-medium text-gray-700">{{ __('SMS Alerts') }}</label>
                            <p class="text-xs text-gray-500">{{ __('Receive SMS notifications') }}</p>
                        </div>
                        <button type="button" wire:click="$toggle('smsAlerts')" role="switch" aria-checked="{{ $smsAlerts ? 'true' : 'false' }}" aria-label="{{ __('Toggle SMS alerts') }}" class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $smsAlerts ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $smsAlerts ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <label for="browser-notifications" class="text-sm font-medium text-gray-700">{{ __('Browser Notifications') }}</label>
                            <p class="text-xs text-gray-500">{{ __('Receive in-browser notifications') }}</p>
                        </div>
                        <button type="button" wire:click="$toggle('browserNotifications')" role="switch" aria-checked="{{ $browserNotifications ? 'true' : 'false' }}" aria-label="{{ __('Toggle browser notifications') }}" class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $browserNotifications ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $browserNotifications ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Save preferences') }}">
                    {{ __('Save Preferences') }}
                </button>
            </div>
        </form>
    </div>
</div>
