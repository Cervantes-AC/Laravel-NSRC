<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Settings') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h4 class="text-sm font-medium text-red-800 mb-2">{{ __('Validation Errors') }}</h4>
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="border-b border-gray-200 mb-6" role="tablist" aria-label="{{ __('Settings tabs') }}">
                        <nav class="flex space-x-4 overflow-x-auto">
                            <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600" role="tab" aria-selected="true" data-tab="branding">
                                {{ __('Branding') }}
                            </button>
                            <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" role="tab" aria-selected="false" data-tab="email">
                                {{ __('Email') }}
                            </button>
                            <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" role="tab" aria-selected="false" data-tab="security">
                                {{ __('Security') }}
                            </button>
                            <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" role="tab" aria-selected="false" data-tab="backup">
                                {{ __('Backup') }}
                            </button>
                            <button type="button" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" role="tab" aria-selected="false" data-tab="notifications">
                                {{ __('Notifications') }}
                            </button>
                        </nav>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.update') }}" id="settings-form">
                        @csrf
                        <input type="hidden" name="group" id="group-field" value="branding" />

                        <!-- Branding Tab -->
                        <div id="tab-branding" class="tab-content" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Branding Settings') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-gray-700">{{ __('Site Name') }}</label>
                                    <input id="site_name" name="site_name" type="text" value="{{ old('site_name', $settings['branding']['site_name'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('site_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="logo" class="block text-sm font-medium text-gray-700">{{ __('Logo URL') }}</label>
                                    <input id="logo" name="logo" type="url" value="{{ old('logo', $settings['branding']['logo'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('logo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="primary_color" class="block text-sm font-medium text-gray-700">{{ __('Primary Color') }}</label>
                                    <input id="primary_color" name="primary_color" type="color" value="{{ old('primary_color', $settings['branding']['primary_color'] ?? '#4f46e5') }}" class="mt-1 block w-16 h-10 rounded border-gray-300" />
                                    @error('primary_color')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="secondary_color" class="block text-sm font-medium text-gray-700">{{ __('Secondary Color') }}</label>
                                    <input id="secondary_color" name="secondary_color" type="color" value="{{ old('secondary_color', $settings['branding']['secondary_color'] ?? '#6366f1') }}" class="mt-1 block w-16 h-10 rounded border-gray-300" />
                                    @error('secondary_color')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Email Tab -->
                        <div id="tab-email" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Email Settings') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="smtp_host" class="block text-sm font-medium text-gray-700">{{ __('SMTP Host') }}</label>
                                    <input id="smtp_host" name="smtp_host" type="text" value="{{ old('smtp_host', $settings['email']['smtp_host'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('smtp_host')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="smtp_port" class="block text-sm font-medium text-gray-700">{{ __('SMTP Port') }}</label>
                                    <input id="smtp_port" name="smtp_port" type="number" value="{{ old('smtp_port', $settings['email']['smtp_port'] ?? '587') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('smtp_port')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="smtp_username" class="block text-sm font-medium text-gray-700">{{ __('SMTP Username') }}</label>
                                    <input id="smtp_username" name="smtp_username" type="text" value="{{ old('smtp_username', $settings['email']['smtp_username'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('smtp_username')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="smtp_password" class="block text-sm font-medium text-gray-700">{{ __('SMTP Password') }}</label>
                                    <input id="smtp_password" name="smtp_password" type="password" value="{{ old('smtp_password', $settings['email']['smtp_password'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('smtp_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="smtp_encryption" class="block text-sm font-medium text-gray-700">{{ __('Encryption') }}</label>
                                    <select id="smtp_encryption" name="smtp_encryption" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('None') }}</option>
                                        <option value="ssl" {{ old('smtp_encryption', $settings['email']['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="tls" {{ old('smtp_encryption', $settings['email']['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    </select>
                                    @error('smtp_encryption')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="from_address" class="block text-sm font-medium text-gray-700">{{ __('From Address') }}</label>
                                    <input id="from_address" name="from_address" type="email" value="{{ old('from_address', $settings['email']['from_address'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('from_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="from_name" class="block text-sm font-medium text-gray-700">{{ __('From Name') }}</label>
                                    <input id="from_name" name="from_name" type="text" value="{{ old('from_name', $settings['email']['from_name'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('from_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div id="tab-security" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Security Settings') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="password_min_length" class="block text-sm font-medium text-gray-700">{{ __('Minimum Password Length') }}</label>
                                    <input id="password_min_length" name="password_min_length" type="number" min="6" max="128" value="{{ old('password_min_length', $settings['security']['password_min_length'] ?? '8') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('password_min_length')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="session_lifetime" class="block text-sm font-medium text-gray-700">{{ __('Session Lifetime (minutes)') }}</label>
                                    <input id="session_lifetime" name="session_lifetime" type="number" min="1" max="1440" value="{{ old('session_lifetime', $settings['security']['session_lifetime'] ?? '120') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('session_lifetime')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="two_factor_enabled" name="two_factor_enabled" type="checkbox" value="1" {{ old('two_factor_enabled', $settings['security']['two_factor_enabled'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="two_factor_enabled" class="text-sm font-medium text-gray-700">{{ __('Require Two-Factor Authentication') }}</label>
                                </div>
                                <div>
                                    <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">{{ __('Max Login Attempts') }}</label>
                                    <input id="max_login_attempts" name="max_login_attempts" type="number" min="1" max="10" value="{{ old('max_login_attempts', $settings['security']['max_login_attempts'] ?? '5') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('max_login_attempts')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Backup Tab -->
                        <div id="tab-backup" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Backup Settings') }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <input id="auto_backup" name="auto_backup" type="checkbox" value="1" {{ old('auto_backup', $settings['backup']['auto_backup'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="auto_backup" class="text-sm font-medium text-gray-700">{{ __('Enable Automatic Backups') }}</label>
                                </div>
                                <div>
                                    <label for="backup_frequency" class="block text-sm font-medium text-gray-700">{{ __('Backup Frequency') }}</label>
                                    <select id="backup_frequency" name="backup_frequency" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="daily" {{ old('backup_frequency', $settings['backup']['backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                        <option value="weekly" {{ old('backup_frequency', $settings['backup']['backup_frequency'] ?? 'daily') === 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                        <option value="monthly" {{ old('backup_frequency', $settings['backup']['backup_frequency'] ?? 'daily') === 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                    </select>
                                    @error('backup_frequency')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="backup_retention_days" class="block text-sm font-medium text-gray-700">{{ __('Retention (days)') }}</label>
                                    <input id="backup_retention_days" name="backup_retention_days" type="number" min="1" max="365" value="{{ old('backup_retention_days', $settings['backup']['backup_retention_days'] ?? '30') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('backup_retention_days')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="backup_location" class="block text-sm font-medium text-gray-700">{{ __('Backup Location') }}</label>
                                    <input id="backup_location" name="backup_location" type="text" value="{{ old('backup_location', $settings['backup']['backup_location'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('backup_location')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notifications Tab -->
                        <div id="tab-notifications" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Notification Settings') }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <input id="email_notifications" name="email_notifications" type="checkbox" value="1" {{ old('email_notifications', $settings['notifications']['email_notifications'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="email_notifications" class="text-sm font-medium text-gray-700">{{ __('Email Notifications') }}</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="push_notifications" name="push_notifications" type="checkbox" value="1" {{ old('push_notifications', $settings['notifications']['push_notifications'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="push_notifications" class="text-sm font-medium text-gray-700">{{ __('Push Notifications') }}</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="duty_reminders" name="duty_reminders" type="checkbox" value="1" {{ old('duty_reminders', $settings['notifications']['duty_reminders'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="duty_reminders" class="text-sm font-medium text-gray-700">{{ __('Duty Reminders') }}</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="report_generation" name="report_generation" type="checkbox" value="1" {{ old('report_generation', $settings['notifications']['report_generation'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="report_generation" class="text-sm font-medium text-gray-700">{{ __('Report Generation') }}</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="system_alerts" name="system_alerts" type="checkbox" value="1" {{ old('system_alerts', $settings['notifications']['system_alerts'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="system_alerts" class="text-sm font-medium text-gray-700">{{ __('System Alerts') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3">
                            <button type="button" onclick="document.getElementById('settings-form').reset()" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                                {{ __('Reset') }}
                            </button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Save settings') }}">
                                {{ __('Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Update group field
            document.getElementById('group-field').value = tabName;
            
            // Update tab buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('text-indigo-600', 'border-indigo-600');
                btn.classList.add('text-gray-500');
                btn.setAttribute('aria-selected', 'false');
            });
            this.classList.remove('text-gray-500');
            this.classList.add('text-indigo-600', 'border-indigo-600');
            this.setAttribute('aria-selected', 'true');

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById('tab-' + tabName).classList.remove('hidden');
        });
    });
</script>
@endpush
