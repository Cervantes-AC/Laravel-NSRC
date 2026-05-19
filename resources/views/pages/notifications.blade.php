<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notifications') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div x-data="notificationCenter(true)" class="space-y-4">
                <template x-if="notifications.length === 0">
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                        <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">No notifications yet</p>
                        <p class="mt-1 text-xs text-slate-500">System notifications will appear here.</p>
                    </div>
                </template>

                <template x-for="notification in notifications" :key="notification.id">
                    <article 
                        class="rounded-lg border p-4 transition-all hover:shadow-md"
                        :class="getNotificationColor(notification.type, notification.data)"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-lg" x-text="getNotificationIcon(notification.type)"></span>
                                    <p class="text-xs font-bold uppercase tracking-wide" x-text="getNotificationTitle(notification)"></p>
                                    <span 
                                        x-show="!notification.read_at"
                                        class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-600 text-white"
                                    >
                                        New
                                    </span>
                                </div>
                                <p class="text-sm mt-1 whitespace-pre-line" x-text="notification.data?.message || notification.data?.title || 'No message'"></p>
                                <p class="text-xs mt-2 opacity-75" x-text="formatTimestamp(notification.created_at)"></p>
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    x-show="!notification.read_at"
                                    @click="markAsRead(notification.id)"
                                    class="px-3 py-1 text-xs font-medium rounded bg-white/50 hover:bg-white/80 transition"
                                >
                                    Mark read
                                </button>
                                <button 
                                    @click="removeNotification(notification.id)"
                                    class="px-3 py-1 text-xs font-medium rounded bg-white/50 hover:bg-white/80 transition"
                                >
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </article>
                </template>

                <div x-show="notifications.length > 0" class="flex gap-3 mt-6">
                    <button 
                        @click="markAllAsRead()"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
                    >
                        Mark all as read
                    </button>
                    <button 
                        @click="loadNotifications()"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition"
                    >
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
