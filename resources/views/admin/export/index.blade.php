<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Export Data') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Accounts') }}</h3>
                                    <p class="text-sm text-gray-500">{{ $stats['accounts'] ?? 0 }} records</p>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.export.accounts') }}" method="GET" class="space-y-3">
                            <div>
                                <input type="text" name="search" placeholder="{{ __('Search...') }}" class="w-full rounded-lg border-gray-300 text-sm" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="format" class="rounded-lg border-gray-300 text-sm">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel</option>
                                    <option value="pdf">PDF</option>
                                </select>
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="send_email" value="1" class="rounded border-gray-300">
                                    {{ __('Email') }}
                                </label>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('Export') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Sessions') }}</h3>
                                    <p class="text-sm text-gray-500">{{ $stats['sessions'] ?? 0 }} records</p>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.export.sessions') }}" method="GET" class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" name="dateFrom" class="rounded-lg border-gray-300 text-sm" />
                                <input type="date" name="dateTo" class="rounded-lg border-gray-300 text-sm" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="format" class="rounded-lg border-gray-300 text-sm">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel</option>
                                    <option value="pdf">PDF</option>
                                </select>
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="send_email" value="1" class="rounded border-gray-300">
                                    {{ __('Email') }}
                                </label>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('Export') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Personnel') }}</h3>
                                    <p class="text-sm text-gray-500">{{ $stats['personnel'] ?? 0 }} records</p>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.export.personnel') }}" method="GET" class="space-y-3">
                            <div>
                                <input type="text" name="search" placeholder="{{ __('Search...') }}" class="w-full rounded-lg border-gray-300 text-sm" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="format" class="rounded-lg border-gray-300 text-sm">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel</option>
                                    <option value="pdf">PDF</option>
                                </select>
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="send_email" value="1" class="rounded border-gray-300">
                                    {{ __('Email') }}
                                </label>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('Export') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Attendance') }}</h3>
                                    <p class="text-sm text-gray-500">{{ $stats['attendance'] ?? 0 }} records</p>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('admin.export.attendance') }}" method="GET" class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" name="dateFrom" class="rounded-lg border-gray-300 text-sm" />
                                <input type="date" name="dateTo" class="rounded-lg border-gray-300 text-sm" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="format" class="rounded-lg border-gray-300 text-sm">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel</option>
                                    <option value="pdf">PDF</option>
                                </select>
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="send_email" value="1" class="rounded border-gray-300">
                                    {{ __('Email') }}
                                </label>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('Export') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Export Options') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('CSV Format') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Comma-separated values. Compatible with Excel, Google Sheets, and most data tools.') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Excel Format') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Native Excel (.xlsx) format with proper formatting and data types.') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Email Export') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Check the "Email" option to receive the export file at aaronclydeccervantes@gmail.com') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
