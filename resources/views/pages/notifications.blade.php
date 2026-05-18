<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Announcements & Notifications') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(isset($announcements) && $announcements->isNotEmpty())
                <div class="mb-6 space-y-3">
                    @foreach($announcements as $announcement)
                        <article class="rounded-lg border {{ $announcement->priority === 'urgent' ? 'border-red-200 bg-red-50' : ($announcement->priority === 'important' ? 'border-amber-200 bg-amber-50' : 'border-blue-200 bg-blue-50') }} p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Announcement') }} · {{ ucfirst($announcement->priority) }}</p>
                                    <h3 class="mt-1 text-base font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                </div>
                                <span class="text-xs text-gray-500">{{ optional($announcement->published_at)->diffForHumans() ?? $announcement->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm text-gray-700">{{ $announcement->body }}</p>
                        </article>
                    @endforeach
                </div>
            @endif

            <div x-data="notificationCenter(true)" class="w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">{{ __('Notification Inbox') }}</h3>
                    <template x-if="unreadCount > 0">
                        <button @click="markAllAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800">{{ __('Mark all read') }}</button>
                    </template>
                </div>

                <div class="max-h-[70vh] overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-8 text-center text-sm text-gray-500">
                            <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            {{ __('No notifications') }}
                        </div>
                    </template>
                    <template x-for="n in notifications" :key="n.id">
                        <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition" :class="n.read_at ? '' : 'bg-indigo-50'">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900" x-text="n.title"></p>
                                <template x-if="n.message">
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="n.message"></p>
                                </template>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="n.created_at"></p>
                            </div>
                            <div class="flex-shrink-0 flex gap-1">
                                <template x-if="!n.read_at">
                                    <button @click="markAsRead(n.id)" class="text-xs text-indigo-600 hover:text-indigo-800">{{ __('Read') }}</button>
                                </template>
                                <button @click="delete(n.id)" class="text-xs text-red-600 hover:text-red-800">{{ __('Del') }}</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
