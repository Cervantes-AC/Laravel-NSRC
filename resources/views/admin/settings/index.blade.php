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
                            <button @click="activeTab = 'notification'" :class="activeTab === 'notification' ? 'border-orange-500 text-orange-600 bg-white' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300'" class="px-4 py-3 border-b-2 font-medium text-sm transition whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notifications
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
