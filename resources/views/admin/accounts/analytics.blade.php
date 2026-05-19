<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('User Activity Analytics') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="text-2xl font-bold text-indigo-600">{{ $analytics['total_users'] }}</div>
                    <div class="text-sm text-gray-500">{{ __('Total Users') }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="text-2xl font-bold text-green-600">{{ $analytics['active_today'] }}</div>
                    <div class="text-sm text-gray-500">{{ __('Active Today') }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="text-2xl font-bold text-blue-600">{{ $analytics['active_this_week'] }}</div>
                    <div class="text-sm text-gray-500">{{ __('Active This Week') }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="text-2xl font-bold text-yellow-600">{{ $analytics['new_this_month'] }}</div>
                    <div class="text-sm text-gray-500">{{ __('New This Month') }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="text-2xl font-bold text-orange-600">{{ $analytics['pending_approval'] }}</div>
                    <div class="text-sm text-gray-500">{{ __('Pending Approval') }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="text-2xl font-bold text-red-600">{{ $analytics['suspended_accounts'] }}</div>
                    <div class="text-sm text-gray-500">{{ __('Suspended') }}</div>
                </div>
            </div>

            {{-- Distribution Charts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-3">{{ __('Role Distribution') }}</h3>
                    <div class="space-y-2">
                        @foreach($analytics['role_distribution'] as $role => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 capitalize">{{ $role }}</span>
                            <div class="flex items-center gap-2">
                                <div class="h-2 bg-indigo-500 rounded" style="width: {{ max(($count / max($analytics['total_users'], 1)) * 200, 20) }}px"></div>
                                <span class="text-sm font-medium">{{ $count }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-3">{{ __('Status Distribution') }}</h3>
                    <div class="space-y-2">
                        @foreach($analytics['status_distribution'] as $status => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 capitalize">{{ $status }}</span>
                            <div class="flex items-center gap-2">
                                <div class="h-2 rounded" style="width: {{ max(($count / max($analytics['total_users'], 1)) * 200, 20) }}px; background: {{ match($status) { 'active' => '#22c55e', 'pending' => '#f59e0b', 'suspended' => '#ef4444', 'rejected' => '#6b7280', 'inactive' => '#9ca3af', default => '#6366f1' } }}"></div>
                                <span class="text-sm font-medium">{{ $count }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Most Active Users --}}
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('Most Active Users') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Role') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Session Count') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Last Login') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($analytics['most_active_users'] as $user)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $user['full_name'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm">{{ $user['email'] }}</td>
                                <td class="px-4 py-2 text-sm capitalize">{{ $user['role'] }}</td>
                                <td class="px-4 py-2 text-sm font-medium">{{ $user['duty_sessions_count'] ?? 0 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $user['last_login_at'] ? \Carbon\Carbon::parse($user['last_login_at'])->diffForHumans() : 'Never' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Registrations & Logins --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-3">{{ __('Recent Registrations (7 days)') }}</h3>
                    <div class="space-y-2">
                        @forelse($analytics['recent_registrations'] as $reg)
                        <div class="flex justify-between text-sm">
                            <span>{{ $reg['full_name'] ?? $reg['email'] }}</span>
                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($reg['created_at'])->diffForHumans() }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">{{ __('No recent registrations.') }}</p>
                        @endforelse
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-3">{{ __('Recent Logins (7 days)') }}</h3>
                    <div class="space-y-2">
                        @forelse($analytics['recent_logins'] as $login)
                        <div class="flex justify-between text-sm">
                            <span>{{ $login['full_name'] ?? $login['email'] }}</span>
                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($login['last_login_at'])->diffForHumans() }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">{{ __('No recent logins.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
