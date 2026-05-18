<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Settings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div x-data="{ activeTab: 'site' }" class="space-y-6">
                {{-- Tabs --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button @click="activeTab = 'site'" :class="activeTab === 'site' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition">
                                Site Settings (10)
                            </button>
                            <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition">
                                Security (15)
                            </button>
                        </nav>
                    </div>

                    {{-- Site Settings Tab --}}
                    <div x-show="activeTab === 'site'" class="p-6">
                        <form method="POST" action="{{ route('admin.settings.update-site') }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Name</label>
                                    <input type="text" name="site_name" value="{{ $siteSettings['site_name']->value ?? 'NSRC AMS' }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Description</label>
                                    <input type="text" name="site_description" value="{{ $siteSettings['site_description']->value ?? '' }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site URL</label>
                                    <input type="url" name="site_url" value="{{ $siteSettings['site_url']->value ?? '' }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="https://example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Admin Email</label>
                                    <input type="email" name="admin_email" value="{{ $siteSettings['admin_email']->value ?? '' }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Support Email</label>
                                    <input type="email" name="support_email" value="{{ $siteSettings['support_email']->value ?? '' }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Phone</label>
                                    <input type="text" name="contact_phone" value="{{ $siteSettings['contact_phone']->value ?? '' }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone</label>
                                    <select name="timezone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php
                                            $timezones = ['Asia/Manila' => 'Asia/Manila (PHT)', 'UTC' => 'UTC', 'America/New_York' => 'America/New_York (EST)', 'America/Chicago' => 'America/Chicago (CST)', 'America/Denver' => 'America/Denver (MST)', 'America/Los_Angeles' => 'America/Los_Angeles (PST)', 'Europe/London' => 'Europe/London (GMT)', 'Europe/Paris' => 'Europe/Paris (CET)', 'Asia/Tokyo' => 'Asia/Tokyo (JST)', 'Asia/Shanghai' => 'Asia/Shanghai (CST)', 'Australia/Sydney' => 'Australia/Sydney (AEST)'];
                                            $currentTimezone = $siteSettings['timezone']->value ?? 'Asia/Manila';
                                        @endphp
                                        @foreach($timezones as $tz => $label)
                                            <option value="{{ $tz }}" {{ $currentTimezone === $tz ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date Format</label>
                                    <select name="date_format" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentDateFormat = $siteSettings['date_format']->value ?? 'Y-m-d'; @endphp
                                        <option value="Y-m-d" {{ $currentDateFormat === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                        <option value="m/d/Y" {{ $currentDateFormat === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="d/m/Y" {{ $currentDateFormat === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="F j, Y" {{ $currentDateFormat === 'F j, Y' ? 'selected' : '' }}>Month Day, Year</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Time Format</label>
                                    <select name="time_format" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentTimeFormat = $siteSettings['time_format']->value ?? 'H:i'; @endphp
                                        <option value="H:i" {{ $currentTimeFormat === 'H:i' ? 'selected' : '' }}>24-hour (HH:MM)</option>
                                        <option value="h:i A" {{ $currentTimeFormat === 'h:i A' ? 'selected' : '' }}>12-hour (HH:MM AM/PM)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Language</label>
                                    <select name="language" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentLanguage = $siteSettings['language']->value ?? 'en'; @endphp
                                        <option value="en" {{ $currentLanguage === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="fil" {{ $currentLanguage === 'fil' ? 'selected' : '' }}>Filipino</option>
                                        <option value="es" {{ $currentLanguage === 'es' ? 'selected' : '' }}>Spanish</option>
                                        <option value="ja" {{ $currentLanguage === 'ja' ? 'selected' : '' }}>Japanese</option>
                                        <option value="zh" {{ $currentLanguage === 'zh' ? 'selected' : '' }}>Chinese</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Site Settings</button>
                            </div>
                        </form>
                    </div>

                    {{-- Security Settings Tab --}}
                    <div x-show="activeTab === 'security'" class="p-6">
                        <form method="POST" action="{{ route('admin.settings.update-security') }}">
                            @csrf
                            <div class="space-y-8">
                                {{-- Password Requirements --}}
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-4">Password Requirements</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Length</label>
                                            <input type="number" name="password_min_length" value="{{ $securitySettings['password_min_length']->value ?? 8 }}" min="6" max="128" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>
                                        <div class="flex items-center pt-6">
                                            <input type="hidden" name="password_require_uppercase" value="0">
                                            <input type="checkbox" name="password_require_uppercase" value="1" id="pwd_uppercase" {{ ($securitySettings['password_require_uppercase']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="pwd_uppercase" class="ml-2 text-sm font-semibold text-gray-700">Require Uppercase Letters</label>
                                        </div>
                                        <div class="flex items-center pt-6">
                                            <input type="hidden" name="password_require_lowercase" value="0">
                                            <input type="checkbox" name="password_require_lowercase" value="1" id="pwd_lowercase" {{ ($securitySettings['password_require_lowercase']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="pwd_lowercase" class="ml-2 text-sm font-semibold text-gray-700">Require Lowercase Letters</label>
                                        </div>
                                        <div class="flex items-center pt-6">
                                            <input type="hidden" name="password_require_numbers" value="0">
                                            <input type="checkbox" name="password_require_numbers" value="1" id="pwd_numbers" {{ ($securitySettings['password_require_numbers']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="pwd_numbers" class="ml-2 text-sm font-semibold text-gray-700">Require Numbers</label>
                                        </div>
                                        <div class="flex items-center pt-6">
                                            <input type="hidden" name="password_require_special" value="0">
                                            <input type="checkbox" name="password_require_special" value="1" id="pwd_special" {{ ($securitySettings['password_require_special']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="pwd_special" class="ml-2 text-sm font-semibold text-gray-700">Require Special Characters</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Session & Login --}}
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-4">Session & Login</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Session Lifetime (minutes)</label>
                                            <input type="number" name="session_lifetime" value="{{ $securitySettings['session_lifetime']->value ?? 120 }}" min="10" max="525600" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>
                                        <div class="flex items-center pt-6">
                                            <input type="hidden" name="two_factor_enabled" value="0">
                                            <input type="checkbox" name="two_factor_enabled" value="1" id="two_factor" {{ ($securitySettings['two_factor_enabled']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="two_factor" class="ml-2 text-sm font-semibold text-gray-700">Enable Two-Factor Authentication</label>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Max Login Attempts</label>
                                            <input type="number" name="max_login_attempts" value="{{ $securitySettings['max_login_attempts']->value ?? 5 }}" min="1" max="100" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Lockout Duration (minutes)</label>
                                            <input type="number" name="lockout_duration" value="{{ $securitySettings['lockout_duration']->value ?? 15 }}" min="1" max="1440" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Rate Limiting --}}
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-4">Rate Limiting</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="flex items-center pt-6">
                                            <input type="hidden" name="enable_rate_limiting" value="0">
                                            <input type="checkbox" name="enable_rate_limiting" value="1" id="rate_limiting" {{ ($securitySettings['enable_rate_limiting']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="rate_limiting" class="ml-2 text-sm font-semibold text-gray-700">Enable Rate Limiting</label>
                                        </div>
                                        <div></div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Max Attempts per Window</label>
                                            <input type="number" name="rate_limit_max_attempts" value="{{ $securitySettings['rate_limit_max_attempts']->value ?? 60 }}" min="1" max="1000" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Decay Window (minutes)</label>
                                            <input type="number" name="rate_limit_decay_minutes" value="{{ $securitySettings['rate_limit_decay_minutes']->value ?? 1 }}" min="1" max="120" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Security Headers --}}
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-4">Security Headers</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input type="hidden" name="enable_hsts" value="0">
                                            <input type="checkbox" name="enable_hsts" value="1" id="hsts" {{ ($securitySettings['enable_hsts']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="hsts" class="ml-2 text-sm font-semibold text-gray-700">Enable HSTS (HTTP Strict Transport Security)</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="hidden" name="enable_csp" value="0">
                                            <input type="checkbox" name="enable_csp" value="1" id="csp" {{ ($securitySettings['enable_csp']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="csp" class="ml-2 text-sm font-semibold text-gray-700">Enable CSP (Content Security Policy)</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="hidden" name="enable_x_frame_options" value="0">
                                            <input type="checkbox" name="enable_x_frame_options" value="1" id="x_frame" {{ ($securitySettings['enable_x_frame_options']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                            <label for="x_frame" class="ml-2 text-sm font-semibold text-gray-700">Enable X-Frame-Options (Clickjacking Protection)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Security Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
