<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 flex-1">
            <div>
                <label for="session-search" class="block text-xs font-medium text-gray-600">{{ __('Search') }}</label>
                <input id="session-search" type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Personnel name...') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>
            <div>
                <label for="session-status" class="block text-xs font-medium text-gray-600">{{ __('Status') }}</label>
                <select id="session-status" wire:model.live="status" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="COMPLETE">{{ __('Complete') }}</option>
                    <option value="ONGOING">{{ __('Ongoing') }}</option>
                    <option value="MISSING_TIMEOUT">{{ __('Missing Time Out') }}</option>
                    <option value="INVALID_LOG">{{ __('Invalid Log') }}</option>
                </select>
            </div>
            <div>
                <label for="session-sector" class="block text-xs font-medium text-gray-600">{{ __('Sector') }}</label>
                <select id="session-sector" wire:model.live="sector" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('All Sectors') }}</option>
                    @foreach($sectors as $sec)
                        <option value="{{ $sec }}">{{ $sec }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="session-from" class="block text-xs font-medium text-gray-600">{{ __('From') }}</label>
                <input id="session-from" type="date" wire:model.live="dateFrom" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>
            <div>
                <label for="session-to" class="block text-xs font-medium text-gray-600">{{ __('To') }}</label>
                <input id="session-to" type="date" wire:model.live="dateTo" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>
        </div>
        <div class="flex items-center gap-2">
            <select wire:model.live="perPage" class="rounded-lg border-gray-300 text-sm shadow-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <button type="button" wire:click="clearFilters" class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">{{ __('Clear') }}</button>
            <a href="{{ route('admin.sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg">+ {{ __('New Session') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($sessions as $session)
            <article class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition" wire:key="session-{{ $session->id }}">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $session->full_name }}</h4>
                        <p class="text-xs text-gray-500">{{ $session->volunteer?->school_id ?? __('No linked account') }}</p>
                    </div>
                    <x-session-status-badge :status="$session->status" />
                </div>
                <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Date') }}</dt>
                        <dd class="font-medium">{{ $session->date?->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Duration') }}</dt>
                        <dd class="font-medium">{{ $session->duration_minutes ? $session->duration_minutes . ' min' : __('Ongoing') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Time In') }}</dt>
                        <dd>{{ $session->time_in?->format('h:i A') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Time Out') }}</dt>
                        <dd>{{ $session->time_out?->format('h:i A') ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500">{{ __('Location / Sector') }}</dt>
                        <dd>{{ $session->location ?? '—' }} · {{ $session->sector ?? __('General') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Integrity') }}</dt>
                        <dd>{{ number_format($session->integrity_score, 0) }}%</dd>
                    </div>
                </dl>
                <div class="mt-4 flex gap-3 text-sm">
                    <a href="{{ route('admin.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-800">{{ __('View') }}</a>
                    <a href="{{ route('admin.sessions.edit', $session) }}" class="text-amber-600 hover:text-amber-800">{{ __('Edit') }}</a>
                </div>
            </article>
        @empty
            <p class="col-span-full text-center py-12 text-gray-500">{{ __('No duty sessions match your filters.') }}</p>
        @endforelse
    </div>

    <div>{{ $sessions->links() }}</div>
</div>
