<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Login History') }} — {{ $user->full_name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Activity Summary') }}</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">{{ __('Total sessions') }}</dt><dd class="font-semibold">{{ $analytics['total_sessions'] ?? 0 }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Total minutes') }}</dt><dd class="font-semibold">{{ $analytics['total_minutes'] ?? 0 }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Average duration') }}</dt><dd class="font-semibold">{{ $analytics['average_duration_minutes'] ?? 0 }} min</dd></div>
                    <div><dt class="text-gray-500">{{ __('Audit events') }}</dt><dd class="font-semibold">{{ $analytics['total_audit_logs'] ?? 0 }}</dd></div>
                </dl>
            </div>
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Action') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Details') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('When') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($history as $log)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $log->action }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($log->details, 80) }}</td>
                                <td class="px-4 py-3 text-sm">{{ $log->created_at?->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">{{ __('No login history recorded.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('admin.accounts.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">{{ __('← Back to accounts') }}</a>
        </div>
    </div>
</x-app-layout>
