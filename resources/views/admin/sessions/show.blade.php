<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Session Details') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <a href="{{ route('admin.sessions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition" aria-label="{{ __('Back to sessions list') }}">
                            &larr; {{ __('Back') }}
                        </a>
                        <a href="{{ route('admin.sessions.edit', $session) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Edit session') }}">
                            {{ __('Edit') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Volunteer Information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Full Name') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->full_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->volunteer->email ?? __('N/A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('School ID') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->volunteer->school_id ?? __('N/A') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Session Information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Date') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->date->format('l, F d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Time In') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->time_in ? $session->time_in->format('h:i:s A') : __('N/A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Time Out') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->time_out ? $session->time_out->format('h:i:s A') : __('N/A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Duration') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->duration ? $session->duration . ' ' . __('minutes') : __('Ongoing') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                    <dd class="text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $session->status === 'completed' ? 'bg-green-100 text-green-800' : ($session->status === 'ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Additional Details') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Integrity Score') }}</dt>
                                    <dd class="text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ ($session->integrity_score ?? 100) >= 80 ? 'bg-green-100 text-green-800' : (($session->integrity_score ?? 100) >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $session->integrity_score ?? 100 }}%
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Location') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->location ?? __('N/A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Sector') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->sector ?? __('N/A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Logged At') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $session->created_at->format('M d, Y h:i A') }}</dd>
                                </div>
                                @if($session->notes)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Notes') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $session->notes }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
