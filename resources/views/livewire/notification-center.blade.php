<div class="relative" x-data="{ open: @js($fullPage) }" aria-label="{{ __('Notifications') }}">
    @if(! $fullPage)
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg" aria-label="{{ __('Toggle notifications') }}">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full" aria-label="{{ __('Unread notifications') }}: {{ $unreadCount }}">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>
    @endif

    <div x-show="open" @click.away="@if(! $fullPage) open = false @endif" class="{{ $fullPage ? 'w-full' : 'absolute right-0 mt-2 w-80 sm:w-96 z-50' }} bg-white border border-gray-200 rounded-lg shadow-lg" role="dialog" aria-label="{{ __('Notification list') }}">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-900">{{ __('Notifications') }}</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-indigo-600 hover:text-indigo-800" aria-label="{{ __('Mark all as read') }}">
                    {{ __('Mark all read') }}
                </button>
            @endif
        </div>

        <div class="{{ $fullPage ? 'max-h-[70vh]' : 'max-h-64' }} overflow-y-auto">
            @forelse($notifications ?? [] as $notification)
                @php
                    $data = $notification->data ?? [];
                    $kind = $data['type'] ?? $data['level'] ?? 'info';
                    $title = $data['title'] ?? $data['subject'] ?? class_basename($notification->type);
                    $message = $data['message'] ?? $data['body'] ?? $data['description'] ?? '';
                @endphp
                <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition {{ $notification->read_at ? '' : 'bg-indigo-50' }}">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($kind === 'info')
                            <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($kind === 'success')
                            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($kind === 'warning')
                            <svg class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @elseif($kind === 'error')
                            <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">{{ $title }}</p>
                        @if($message)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $message }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex-shrink-0 flex gap-1">
                        @if(!$notification->read_at)
                            <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs text-indigo-600 hover:text-indigo-800" aria-label="{{ __('Mark notification as read') }}">{{ __('Read') }}</button>
                        @endif
                        <button wire:click="delete('{{ $notification->id }}')" class="text-xs text-red-600 hover:text-red-800" aria-label="{{ __('Delete notification') }}">{{ __('Del') }}</button>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-500">
                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    {{ __('No notifications') }}
                </div>
            @endforelse
        </div>

        @if(count($notifications ?? []) > 0)
            <div class="px-4 py-2 border-t border-gray-200 text-center">
                <a href="{{ route('notifications.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">{{ __('View all notifications') }}</a>
            </div>
        @endif
    </div>
</div>
