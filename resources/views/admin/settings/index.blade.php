<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Settings') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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

                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf

                        <div id="tab-branding" class="tab-content" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Branding Settings') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="app_name" class="block text-sm font-medium text-gray-700">{{ __('Application Name') }}</label>
                                    <input id="app_name" name="app_name" type="text" value="{{ old('app_name', config('app.name')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <label for="app_logo" class="block text-sm font-medium text-gray-700">{{ __('Logo URL') }}</label>
                                    <input id="app_logo" name="app_logo" type="url" value="{{ old('app_logo', config('app.logo')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <label for="primary_color" class="block text-sm font-medium text-gray-700">{{ __('Primary Color') }}</label>
                                    <input id="primary_color" name="primary_color" type="color" value="{{ old('primary_color', '#4f46e5') }}" class="mt-1 block w-16 h-10 rounded border-gray-300" />
                                </div>
                            </div>
                        </div>

                        <div id="tab-email" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Email Settings') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="mail_driver" class="block text-sm font-medium text-gray-700">{{ __('Mail Driver') }}</label>
                                    <select id="mail_driver" name="mail_driver" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="smtp" {{ old('mail_driver', config('mail.default')) === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ old('mail_driver', config('mail.default')) === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="mailgun" {{ old('mail_driver', config('mail.default')) === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="mail_host" class="block text-sm font-medium text-gray-700">{{ __('Mail Host') }}</label>
                                    <input id="mail_host" name="mail_host" type="text" value="{{ old('mail_host', config('mail.mailers.smtp.host')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <label for="mail_port" class="block text-sm font-medium text-gray-700">{{ __('Mail Port') }}</label>
                                    <input id="mail_port" name="mail_port" type="number" value="{{ old('mail_port', config('mail.mailers.smtp.port')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <label for="mail_from" class="block text-sm font-medium text-gray-700">{{ __('From Address') }}</label>
                                    <input id="mail_from" name="mail_from" type="email" value="{{ old('mail_from', config('mail.from.address')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <div id="tab-security" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Security Settings') }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <input id="two_factor" name="two_factor" type="checkbox" value="1" {{ old('two_factor', config('app.two_factor')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="two_factor" class="text-sm font-medium text-gray-700">{{ __('Require Two-Factor Authentication') }}</label>
                                </div>
                                <div>
                                    <label for="session_timeout" class="block text-sm font-medium text-gray-700">{{ __('Session Timeout (minutes)') }}</label>
                                    <input id="session_timeout" name="session_timeout" type="number" value="{{ old('session_timeout', config('session.lifetime')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                                <div>
                                    <label for="max_login_attempts" class="block text-sm font-medium text-gray-700">{{ __('Max Login Attempts') }}</label>
                                    <input id="max_login_attempts" name="max_login_attempts" type="number" value="{{ old('max_login_attempts', 5) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <div id="tab-backup" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Backup Settings') }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <input id="auto_backup" name="auto_backup" type="checkbox" value="1" {{ old('auto_backup') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="auto_backup" class="text-sm font-medium text-gray-700">{{ __('Enable Automatic Backups') }}</label>
                                </div>
                                <div>
                                    <label for="backup_frequency" class="block text-sm font-medium text-gray-700">{{ __('Backup Frequency') }}</label>
                                    <select id="backup_frequency" name="backup_frequency" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="daily">{{ __('Daily') }}</option>
                                        <option value="weekly">{{ __('Weekly') }}</option>
                                        <option value="monthly">{{ __('Monthly') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="backup_retention" class="block text-sm font-medium text-gray-700">{{ __('Retention (days)') }}</label>
                                    <input id="backup_retention" name="backup_retention" type="number" value="{{ old('backup_retention', 30) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <div id="tab-notifications" class="tab-content hidden" role="tabpanel">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Notification Settings') }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <input id="email_notifications" name="email_notifications" type="checkbox" value="1" {{ old('email_notifications', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="email_notifications" class="text-sm font-medium text-gray-700">{{ __('Email Notifications') }}</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="sms_notifications" name="sms_notifications" type="checkbox" value="1" {{ old('sms_notifications') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="sms_notifications" class="text-sm font-medium text-gray-700">{{ __('SMS Notifications') }}</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="in_app_notifications" name="in_app_notifications" type="checkbox" value="1" {{ old('in_app_notifications', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <label for="in_app_notifications" class="text-sm font-medium text-gray-700">{{ __('In-App Notifications') }}</label>
                                </div>
                                <div>
                                    <label for="notify_on" class="block text-sm font-medium text-gray-700">{{ __('Notify On') }}</label>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center gap-3">
                                            <input id="notify_new_user" name="notify_events[]" type="checkbox" value="new_user" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                            <label for="notify_new_user" class="text-sm text-gray-700">{{ __('New User Registration') }}</label>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <input id="notify_session_anomaly" name="notify_events[]" type="checkbox" value="session_anomaly" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                            <label for="notify_session_anomaly" class="text-sm text-gray-700">{{ __('Session Anomaly Detected') }}</label>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <input id="notify_backup_fail" name="notify_events[]" type="checkbox" value="backup_fail" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                            <label for="notify_backup_fail" class="text-sm text-gray-700">{{ __('Backup Failure') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end">
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
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('text-indigo-600', 'border-indigo-600');
                btn.classList.add('text-gray-500');
                btn.setAttribute('aria-selected', 'false');
            });
            this.classList.remove('text-gray-500');
            this.classList.add('text-indigo-600', 'border-indigo-600');
            this.setAttribute('aria-selected', 'true');

            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
        });
    });
</script>
@endpush
