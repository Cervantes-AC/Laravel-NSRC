<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notification Details') }}</h2>
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">&larr; Back to Notifications</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center gap-2">
                        @if($notification->severity === 'critical')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">CRITICAL</span>
                        @elseif($notification->severity === 'warning')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">WARNING</span>
                        @elseif($notification->severity === 'error')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">ERROR</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">INFO</span>
                        @endif
                        <span class="text-sm font-medium text-gray-500">{{ $notification->type }}</span>
                        @if(!$notification->read_at)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-600 text-white">Unread</span>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                        <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ $notification->data['message'] ?? $notification->data['description'] ?? 'No message content.' }}</p>
                    </div>

                    {{-- Metadata --}}
                    <div class="border-t pt-4">
                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500">Category</dt>
                                <dd class="font-medium text-gray-900">{{ $notification->category ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Created</dt>
                                <dd class="font-medium text-gray-900">{{ $notification->created_at->format('M d, Y h:i A') }}</dd>
                            </div>
                            @if($notification->read_at)
                                <div>
                                    <dt class="text-gray-500">Read at</dt>
                                    <dd class="font-medium text-gray-900">{{ $notification->read_at->format('M d, Y h:i A') }}</dd>
                                </div>
                            @endif
                            @if($notification->acknowledged_at)
                                <div>
                                    <dt class="text-gray-500">Acknowledged at</dt>
                                    <dd class="font-medium text-gray-900">{{ $notification->acknowledged_at->format('M d, Y h:i A') }}</dd>
                                </div>
                            @endif
                            @if($notification->failure_reason)
                                <div class="col-span-2">
                                    <dt class="text-red-600">Failure Reason</dt>
                                    <dd class="font-medium text-gray-900">{{ $notification->failure_reason }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Data Payload --}}
                    @if(!empty($notification->data))
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Additional Data</h4>
                            <pre class="text-xs bg-gray-50 rounded-lg p-4 overflow-x-auto">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex gap-2">
                    @if(!$notification->read_at)
                        <form method="POST" action="{{ route('notifications.mark-as-read', $notification) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">Mark as Read</button>
                        </form>
                    @endif
                    @if(!$notification->acknowledged_at)
                        <form method="POST" action="{{ route('notifications.acknowledge', $notification) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700 transition">Acknowledge</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>