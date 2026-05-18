<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Backup Management') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Run Backup') }}</h3>
                            <form method="POST" action="{{ route('admin.backup.run') }}" class="space-y-6">
                                @csrf

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Backup Type') }}</label>
                                    @error('type')
                                        <p class="mb-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                                    @enderror
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <label for="type-database" class="relative flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                            <input id="type-database" type="radio" name="type" value="database" class="sr-only" aria-label="{{ __('Database backup') }}" />
                                            <div class="text-center">
                                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                                </svg>
                                                <span class="mt-2 block text-sm font-medium text-gray-900">{{ __('Database') }}</span>
                                                <span class="text-xs text-gray-500">{{ __('SQL dump only') }}</span>
                                            </div>
                                        </label>

                                        <label for="type-files" class="relative flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                            <input id="type-files" type="radio" name="type" value="files" class="sr-only" aria-label="{{ __('Files backup') }}" />
                                            <div class="text-center">
                                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                </svg>
                                                <span class="mt-2 block text-sm font-medium text-gray-900">{{ __('Files') }}</span>
                                                <span class="text-xs text-gray-500">{{ __('Uploads & config') }}</span>
                                            </div>
                                        </label>

                                        <label for="type-full" class="relative flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                            <input id="type-full" type="radio" name="type" value="full" class="sr-only" aria-label="{{ __('Full backup') }}" />
                                            <div class="text-center">
                                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                                <span class="mt-2 block text-sm font-medium text-gray-900">{{ __('Full') }}</span>
                                                <span class="text-xs text-gray-500">{{ __('Database & files') }}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description (optional)') }}</label>
                                    <input id="description" name="description" type="text" value="{{ old('description') }}" placeholder="{{ __('e.g. Before update v2.1') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>

                                <div class="flex items-center gap-4">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Run backup') }}">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        {{ __('Run Backup') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Recent Backups') }}</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Recent backups table') }}">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Size') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Description') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($backupLogs ?? [] as $backup)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $backup->created_at->format('M d, Y h:i A') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $backup->type === 'full' ? 'bg-purple-100 text-purple-800' : ($backup->type === 'database' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ ucfirst($backup->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $backup->size ?? __('N/A') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $backup->status === 'completed' ? 'bg-green-100 text-green-800' : ($backup->status === 'running' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ ucfirst($backup->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $backup->description ?? __('N/A') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a href="{{ route('admin.backup.download', $backup) }}" class="text-indigo-600 hover:text-indigo-900" aria-label="{{ __('Download backup') }}">{{ __('Download') }}</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No backups found.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $backupLogs->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Retention Policy') }}</h3>
                            <div class="space-y-4">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <dl class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">{{ __('Max Backups') }}</dt>
                                            <dd class="text-gray-900 font-medium">{{ $retention ?? 10 }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">{{ __('Retention Period') }}</dt>
                                            <dd class="text-gray-900 font-medium">{{ $retentionDays ?? 30 }} {{ __('days') }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">{{ __('Total Backups') }}</dt>
                                            <dd class="text-gray-900 font-medium">{{ $backupLogs?->total() ?? count($backupLogs ?? []) }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">{{ __('Total Size') }}</dt>
                                            <dd class="text-gray-900 font-medium">{{ $totalSize ?? '0 B' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <p class="text-xs text-gray-500">{{ __('Oldest backups are automatically deleted when new ones are created and the limit is exceeded.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
