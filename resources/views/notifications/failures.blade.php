<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Failed Notifications') }}</h2>
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">&larr; Back to Notifications</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistics --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_failures'] }}</p>
                    <p class="text-xs text-gray-500">Total Failures</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-2xl font-bold text-red-600">{{ $stats['critical_failures'] }}</p>
                    <p class="text-xs text-gray-500">Critical</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['warning_failures'] }}</p>
                    <p class="text-xs text-gray-500">Warnings</p>
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <p class="text-2xl font-bold text-gray-600">{{ $stats['unacknowledged_failures'] }}</p>
                    <p class="text-xs text-gray-500">Unacknowledged</p>
                </div>
            </div>

            {{-- Failures List --}}
            @if($notifications->count() > 0)
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        <div class="bg-white rounded-lg border p-4 @if($notification->severity === 'critical') border-l-4 border-l-red-500 @elseif($notification->severity === 'warning') border-l-4 border-l-amber-500 @endif">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if($notification->severity === 'critical')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">CRITICAL</span>
                                        @elseif($notification->severity === 'warning')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">WARNING</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">ERROR</span>
                                        @endif
                                        <span class="text-xs font-medium text-gray-500">{{ $notification->type }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-1">{{ $notification->data['message'] ?? $notification->data['title'] ?? 'No message' }}</p>
                                    @if($notification->failure_reason)
                                        <p class="text-xs text-red-600 mt-1">Reason: {{ $notification->failure_reason }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('notifications.show', $notification) }}" class="px-3 py-1 text-xs font-medium rounded bg-white border border-gray-300 hover:bg-gray-50 transition">View</a>
                                    @if(!$notification->acknowledged_at)
                                        <form method="POST" action="{{ route('notifications.acknowledge', $notification) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 text-xs font-medium rounded bg-amber-600 text-white hover:bg-amber-700 transition">Acknowledge</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                    <p class="text-sm font-semibold text-slate-700">No failures recorded</p>
                    <p class="mt-1 text-xs text-slate-500">All systems are operating normally.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>