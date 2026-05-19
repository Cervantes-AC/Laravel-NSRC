<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('System Settings') }}</h2>
            <span class="text-sm text-gray-500">Manage all site configurations</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 flex items-center gap-3" role="alert">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tab Navigation --}}
            <div x-data="{ activeTab: 'site' }" class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50">
                        <nav class="flex flex-wrap -mb-px">
                            <button @click="activeTab = 'site'" :class="activeTab === 'site' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Site Settings
                            </button>
                            <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Security
                            </button>
                            <button @click="activeTab = 'email'" :class="activeTab === 'email' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Email
                            </button>
                            <button @click="activeTab = 'backup'" :class="activeTab === 'backup' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Backup
                            </button>
                            <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                Branding
                            </button>
                            <button @click="activeTab = 'notification'" :class="activeTab === 'notification' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notifications
                            </button>
                            <button @click="activeTab = 'api'" :class="activeTab === 'api' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                API Settings
                            </button>
                        </nav>
                    </div>

                    {{-- Site Settings Tab --}}
                    <div x-show="activeTab === 'site'" class="p-6 space-y-6">
                        <form method="POST" action="{{ route('admin.settings.update-site') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Name *</label>
                                    <input type="text" name="site_name" value="{{ $siteSettings['site_name']->value ?? 'NSRC AMS' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                    <p class="text-xs text-gray-500 mt-1">The name displayed throughout the application</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Description</label>
                                    <input type="text" name="site_description" value="{{ $siteSettings['site_description']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <p class="text-xs text-gray-500 mt-1">Brief description of your site</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site URL</label>
                                    <input type="url" name="site_url" value="{{ $siteSettings['site_url']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="https://example.com">
                                    <p class="text-xs text-gray-500 mt-1">Your site's public URL</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Admin Email</label>
                                    <input type="email" name="admin_email" value="{{ $siteSettings['admin_email']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <p class="text-xs text-gray-500 mt-1">Primary admin contact email</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Support Email</label>
                                    <input type="email" name="support_email" value="{{ $siteSettings['support_email']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <p class="text-xs text-gray-500 mt-1">Support team contact email</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Phone</label>
                                    <input type="text" name="contact_phone" value="{{ $siteSettings['contact_phone']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <p class="text-xs text-gray-500 mt-1">Main contact phone number</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone *</label>
                                    <select name="timezone" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date Format *</label>
                                    <select name="date_format" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentDateFormat = $siteSettings['date_format']->value ?? 'Y-m-d'; @endphp
                                        <option value="Y-m-d" {{ $currentDateFormat === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                        <option value="m/d/Y" {{ $currentDateFormat === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="d/m/Y" {{ $currentDateFormat === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="F j, Y" {{ $currentDateFormat === 'F j, Y' ? 'selected' : '' }}>Month Day, Year</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Time Format *</label>
                                    <select name="time_format" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentTimeFormat = $siteSettings['time_format']->value ?? 'H:i'; @endphp
                                        <option value="H:i" {{ $currentTimeFormat === 'H:i' ? 'selected' : '' }}>24-hour (HH:MM)</option>
                                        <option value="h:i A" {{ $currentTimeFormat === 'h:i A' ? 'selected' : '' }}>12-hour (HH:MM AM/PM)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Language *</label>
                                    <select name="language" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentLanguage = $siteSettings['language']->value ?? 'en'; @endphp
                                        <option value="en" {{ $currentLanguage === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="fil" {{ $currentLanguage === 'fil' ? 'selected' : '' }}>Filipino</option>
                                        <option value="es" {{ $currentLanguage === 'es' ? 'selected' : '' }}>Spanish</option>
                                        <option value="ja" {{ $currentLanguage === 'ja' ? 'selected' : '' }}>Japanese</option>
                                        <option value="zh" {{ $currentLanguage === 'zh' ? 'selected' : '' }}>Chinese</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Maintenance Mode --}}
                            <div class="border-t pt-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Maintenance Mode</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="hidden" name="maintenance_mode" value="0">
                                        <input type="checkbox" name="maintenance_mode" value="1" id="maintenance_mode" {{ ($siteSettings['maintenance_mode']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <label for="maintenance_mode" class="ml-3 text-sm font-semibold text-gray-700">Enable Maintenance Mode</label>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Maintenance Message</label>
                                        <textarea name="maintenance_message" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="System is under maintenance...">{{ $siteSettings['maintenance_message']->value ?? '' }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">Message shown to users when maintenance mode is enabled</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Site Settings</button>
                            </div>
                        </form>
                    </div>

                    {{-- Branding Settings Tab --}}
                    <div x-show="activeTab === 'branding'" class="p-6 space-y-6" style="display: none;">
                        <form method="POST" action="{{ route('admin.settings.update-branding') }}" class="space-y-6" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Logo</label>
                                    <div class="space-y-3">
                                        @php $logoPath = $brandingSettings['branding_logo']->value ?? ''; @endphp
                                        @if($logoPath && Storage::disk('public')->exists($logoPath))
                                            <div class="w-32 h-32 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                                                <img src="{{ asset('storage/' . $logoPath) }}" alt="Site logo" class="max-w-full max-h-full object-contain">
                                            </div>
                                        @endif
                                        <input type="file" name="branding_logo" accept="image/png,image/jpeg,image/svg+xml" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                        <p class="text-xs text-gray-500">Recommended: 200x200px PNG or SVG</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Favicon</label>
                                    <div class="space-y-3">
                                        @php $faviconPath = $brandingSettings['branding_favicon']->value ?? ''; @endphp
                                        @if($faviconPath && Storage::disk('public')->exists($faviconPath))
                                            <div class="w-10 h-10 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                                                <img src="{{ asset('storage/' . $faviconPath) }}" alt="Favicon" class="max-w-full max-h-full object-contain">
                                            </div>
                                        @endif
                                        <input type="file" name="branding_favicon" accept="image/png,image/x-icon,image/svg+xml" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                        <p class="text-xs text-gray-500">Recommended: 32x32px PNG or ICO</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" name="branding_primary_color" value="{{ $brandingSettings['branding_primary_color']->value ?? '#f97316' }}" class="h-10 w-16 rounded border border-gray-300 cursor-pointer">
                                        <span class="text-sm text-gray-500">{{ $brandingSettings['branding_primary_color']->value ?? '#f97316' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Primary accent color (buttons, links, highlights)</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary Color</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" name="branding_secondary_color" value="{{ $brandingSettings['branding_secondary_color']->value ?? '#3b82f6' }}" class="h-10 w-16 rounded border border-gray-300 cursor-pointer">
                                        <span class="text-sm text-gray-500">{{ $brandingSettings['branding_secondary_color']->value ?? '#3b82f6' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Secondary accent color</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Accent Color</label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" name="branding_accent_color" value="{{ $brandingSettings['branding_accent_color']->value ?? '#10b981' }}" class="h-10 w-16 rounded border border-gray-300 cursor-pointer">
                                        <span class="text-sm text-gray-500">{{ $brandingSettings['branding_accent_color']->value ?? '#10b981' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Accent color for success states and highlights</p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Branding Settings</button>
                            </div>
                        </form>
                    </div>

                    {{-- Security Settings Tab --}}
                    <div x-show="activeTab === 'security'" class="p-6 space-y-6" style="display: none;">
                        <form method="POST" action="{{ route('admin.settings.update-security') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Min Length *</label>
                                    <input type="number" name="password_min_length" value="{{ $securitySettings['password_min_length']->value ?? '8' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="6">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Session Lifetime (minutes) *</label>
                                    <input type="number" name="session_lifetime" value="{{ $securitySettings['session_lifetime']->value ?? '120' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="10">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Login Attempts *</label>
                                    <input type="number" name="max_login_attempts" value="{{ $securitySettings['max_login_attempts']->value ?? '5' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lockout Duration (minutes) *</label>
                                    <input type="number" name="lockout_duration" value="{{ $securitySettings['lockout_duration']->value ?? '15' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rate Limit Max Attempts *</label>
                                    <input type="number" name="rate_limit_max_attempts" value="{{ $securitySettings['rate_limit_max_attempts']->value ?? '60' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rate Limit Decay Minutes *</label>
                                    <input type="number" name="rate_limit_decay_minutes" value="{{ $securitySettings['rate_limit_decay_minutes']->value ?? '1' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1">
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Password Requirements</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="hidden" name="password_require_uppercase" value="0">
                                        <input type="checkbox" name="password_require_uppercase" value="1" {{ ($securitySettings['password_require_uppercase']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Require uppercase letter</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="password_require_lowercase" value="0">
                                        <input type="checkbox" name="password_require_lowercase" value="1" {{ ($securitySettings['password_require_lowercase']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Require lowercase letter</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="password_require_numbers" value="0">
                                        <input type="checkbox" name="password_require_numbers" value="1" {{ ($securitySettings['password_require_numbers']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Require numbers</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="password_require_special" value="0">
                                        <input type="checkbox" name="password_require_special" value="1" {{ ($securitySettings['password_require_special']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Require special characters</span>
                                    </label>
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Security Features</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="hidden" name="two_factor_enabled" value="0">
                                        <input type="checkbox" name="two_factor_enabled" value="1" {{ ($securitySettings['two_factor_enabled']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable Two-Factor Authentication</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="enable_rate_limiting" value="0">
                                        <input type="checkbox" name="enable_rate_limiting" value="1" {{ ($securitySettings['enable_rate_limiting']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable Rate Limiting</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="enable_hsts" value="0">
                                        <input type="checkbox" name="enable_hsts" value="1" {{ ($securitySettings['enable_hsts']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable HSTS</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="enable_csp" value="0">
                                        <input type="checkbox" name="enable_csp" value="1" {{ ($securitySettings['enable_csp']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable Content Security Policy</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="enable_x_frame_options" value="0">
                                        <input type="checkbox" name="enable_x_frame_options" value="1" {{ ($securitySettings['enable_x_frame_options']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable X-Frame-Options</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Security Settings</button>
                            </div>
                        </form>
                    </div>

                    {{-- Email Settings Tab --}}
                    <div x-show="activeTab === 'email'" class="p-6 space-y-6" style="display: none;">
                        <form method="POST" action="{{ route('admin.settings.update-email') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail From Address *</label>
                                    <input type="email" name="mail_from_address" value="{{ $emailSettings['mail_from_address']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail From Name *</label>
                                    <input type="text" name="mail_from_name" value="{{ $emailSettings['mail_from_name']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail Mailer *</label>
                                    <select name="mail_mailer" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentMailer = $emailSettings['mail_mailer']->value ?? 'log'; @endphp
                                        <option value="log" {{ $currentMailer === 'log' ? 'selected' : '' }}>Log</option>
                                        <option value="smtp" {{ $currentMailer === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mailgun" {{ $currentMailer === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="postmark" {{ $currentMailer === 'postmark' ? 'selected' : '' }}>Postmark</option>
                                        <option value="sendmail" {{ $currentMailer === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail Host</label>
                                    <input type="text" name="mail_host" value="{{ $emailSettings['mail_host']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail Port</label>
                                    <input type="number" name="mail_port" value="{{ $emailSettings['mail_port']->value ?? '587' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail Username</label>
                                    <input type="text" name="mail_username" value="{{ $emailSettings['mail_username']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail Password</label>
                                    <input type="password" name="mail_password" value="{{ $emailSettings['mail_password']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mail Encryption</label>
                                    <select name="mail_encryption" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                        @php $currentEncryption = $emailSettings['mail_encryption']->value ?? ''; @endphp
                                        <option value="" {{ $currentEncryption === '' ? 'selected' : '' }}>None</option>
                                        <option value="tls" {{ $currentEncryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ $currentEncryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <label class="flex items-center">
                                    <input type="hidden" name="enable_email_notifications" value="0">
                                    <input type="checkbox" name="enable_email_notifications" value="1" {{ ($emailSettings['enable_email_notifications']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                    <span class="ml-3 text-sm font-semibold text-gray-700">Enable Email Notifications</span>
                                </label>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Email Settings</button>
                            </div>
                        </form>
                    </div>

                    {{-- Backup Settings Tab --}}
                    <div x-show="activeTab === 'backup'" class="p-6 space-y-6" style="display: none;">
                        <form method="POST" action="{{ route('admin.settings.update-backup') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Backup Frequency *</label>
                                    <select name="backup_frequency" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                                        @php $currentFreq = $backupSettings['backup_frequency']->value ?? 'weekly'; @endphp
                                        <option value="daily" {{ $currentFreq === 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ $currentFreq === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $currentFreq === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Retention Days *</label>
                                    <input type="number" name="backup_retention_days" value="{{ $backupSettings['backup_retention_days']->value ?? '30' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1" max="365">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Backup Notification Email</label>
                                    <input type="email" name="backup_notification_email" value="{{ $backupSettings['backup_notification_email']->value ?? '' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Backup Options</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="hidden" name="backup_enabled" value="0">
                                        <input type="checkbox" name="backup_enabled" value="1" {{ ($backupSettings['backup_enabled']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable Automated Backups</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="backup_include_files" value="0">
                                        <input type="checkbox" name="backup_include_files" value="1" {{ ($backupSettings['backup_include_files']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Include File Uploads</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="backup_include_database" value="0">
                                        <input type="checkbox" name="backup_include_database" value="1" {{ ($backupSettings['backup_include_database']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Include Database</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Backup Settings</button>
                            </div>
                        </form>
                    </div>

                    {{-- Notification Settings Tab --}}
                    <div x-show="activeTab === 'notification'" class="p-6 space-y-6" style="display: none;">
                        <form method="POST" action="{{ route('admin.settings.update-notification') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notification Retention (days) *</label>
                                    <input type="number" name="notification_retention_days" value="{{ $notificationSettings['notification_retention_days']->value ?? '30' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1" max="365">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Batch Interval (minutes) *</label>
                                    <input type="number" name="notification_batch_interval" value="{{ $notificationSettings['notification_batch_interval']->value ?? '5' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1" max="1440">
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Notification Preferences</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="hidden" name="notification_enabled" value="0">
                                        <input type="checkbox" name="notification_enabled" value="1" {{ ($notificationSettings['notification_enabled']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable Notifications</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="notification_email_alerts" value="0">
                                        <input type="checkbox" name="notification_email_alerts" value="1" {{ ($notificationSettings['notification_email_alerts']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Email Alert Notifications</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="notification_system_alerts" value="0">
                                        <input type="checkbox" name="notification_system_alerts" value="1" {{ ($notificationSettings['notification_system_alerts']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">System Alert Notifications</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="hidden" name="notification_batch_send" value="0">
                                        <input type="checkbox" name="notification_batch_send" value="1" {{ ($notificationSettings['notification_batch_send']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Batch Send Notifications</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save Notification Settings</button>
                            </div>
                        </form>
                    </div>

                    {{-- API Settings Tab --}}
                    <div x-show="activeTab === 'api'" class="p-6 space-y-6" style="display: none;">
                        <form method="POST" action="{{ route('admin.settings.update-api') }}" class="space-y-6">
                            @csrf
                            <h3 class="text-base font-semibold text-gray-900">API Access</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="hidden" name="api_enabled" value="0">
                                    <input type="checkbox" name="api_enabled" value="1" {{ ($apiSettings['api_enabled']->value ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                    <span class="ml-3 text-sm font-semibold text-gray-700">Enable API Access</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="hidden" name="api_key_required" value="0">
                                    <input type="checkbox" name="api_key_required" value="1" {{ ($apiSettings['api_key_required']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                    <span class="ml-3 text-sm text-gray-700">Require API Key for Authentication</span>
                                </label>
                            </div>

                            <div class="border-t pt-6">
                                <h3 class="text-base font-semibold text-gray-900 mb-4">Rate Limiting</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Max Requests Per Minute *</label>
                                        <input type="number" name="api_rate_limit_max_attempts" value="{{ $apiSettings['api_rate_limit_max_attempts']->value ?? '60' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Decay Time (minutes) *</label>
                                        <input type="number" name="api_rate_limit_decay_minutes" value="{{ $apiSettings['api_rate_limit_decay_minutes']->value ?? '1' }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" required min="1">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="flex items-center">
                                        <input type="hidden" name="api_rate_limit_enabled" value="0">
                                        <input type="checkbox" name="api_rate_limit_enabled" value="1" {{ ($apiSettings['api_rate_limit_enabled']->value ?? '1') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500">
                                        <span class="ml-3 text-sm text-gray-700">Enable Rate Limiting</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t">
                                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-sm transition">Save API Settings</button>
                            </div>
                        </form>

                        {{-- API Key Management --}}
                        <div class="border-t pt-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">API Key Management</h3>

                            <form method="POST" action="{{ route('admin.settings.generate-api-key') }}" class="flex items-end gap-3 mb-6">
                                @csrf
                                <div class="flex-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">New API Key Name</label>
                                    <input type="text" name="name" class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="e.g., Production App, Mobile App" required>
                                </div>
                                <button type="submit" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-sm transition">Generate Key</button>
                            </form>

                            @if($apiKeys->count() > 0)
                                <div class="overflow-hidden border border-gray-200 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Key (hashed)</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Last Used</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($apiKeys as $apiKey)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $apiKey->name }}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ substr($apiKey->key, 0, 20) }}...</td>
                                                    <td class="px-4 py-3">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $apiKey->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ ucfirst($apiKey->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : 'Never' }}</td>
                                                    <td class="px-4 py-3 text-sm space-x-2">
                                                        @if($apiKey->status === 'active')
                                                            <form method="POST" action="{{ route('admin.settings.revoke-api-key', $apiKey) }}" class="inline" onsubmit="return confirm('Revoke this API key?')">
                                                                @csrf
                                                                <button type="submit" class="text-amber-600 hover:text-amber-800 font-medium">Revoke</button>
                                                            </form>
                                                        @endif
                                                        <form method="POST" action="{{ route('admin.settings.delete-api-key', $apiKey) }}" class="inline" onsubmit="return confirm('Delete this API key permanently?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 text-sm text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                    No API keys generated yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Reset to Defaults --}}
                    <div class="border-t border-gray-200 p-6 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Reset to Defaults</h3>
                                <p class="text-xs text-gray-500 mt-1">Reset all settings in the current tab to their default values</p>
                            </div>
                            <form method="POST" action="{{ route('admin.settings.reset') }}" onsubmit="return confirm('Are you sure you want to reset all settings in this tab to defaults?')">
                                @csrf
                                <input type="hidden" name="group" x-bind:value="activeTab">
                                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-sm transition text-sm">Reset Tab to Defaults</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
