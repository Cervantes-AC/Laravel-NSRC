<div class="space-y-6" aria-label="{{ __('Analytics') }}">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Analytics') }}</h3>
        <div class="flex gap-2" role="tablist" aria-label="{{ __('Period filter') }}">
            <button wire:click="$set('period', 'daily')" class="px-3 py-1.5 text-sm rounded-lg transition {{ $period === 'daily' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" role="tab" aria-selected="{{ $period === 'daily' ? 'true' : 'false' }}" aria-label="{{ __('Daily view') }}">
                {{ __('Daily') }}
            </button>
            <button wire:click="$set('period', 'weekly')" class="px-3 py-1.5 text-sm rounded-lg transition {{ $period === 'weekly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" role="tab" aria-selected="{{ $period === 'weekly' ? 'true' : 'false' }}" aria-label="{{ __('Weekly view') }}">
                {{ __('Weekly') }}
            </button>
            <button wire:click="$set('period', 'monthly')" class="px-3 py-1.5 text-sm rounded-lg transition {{ $period === 'monthly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" role="tab" aria-selected="{{ $period === 'monthly' ? 'true' : 'false' }}" aria-label="{{ __('Monthly view') }}">
                {{ __('Monthly') }}
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="h-64 flex items-center justify-center border-2 border-dashed border-gray-200 rounded-lg">
            <canvas id="analytics-chart" aria-label="{{ __('Analytics chart') }}" wire:ignore></canvas>
            <p class="text-sm text-gray-400">{{ __('Chart will render here') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
            <dt class="text-sm font-medium text-gray-500">{{ __('Total Sessions') }}</dt>
            <dd class="mt-2 text-2xl font-bold text-gray-900">{{ $totalSessions ?? 0 }}</dd>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
            <dt class="text-sm font-medium text-gray-500">{{ __('Total Hours') }}</dt>
            <dd class="mt-2 text-2xl font-bold text-indigo-600">{{ $totalHours ?? 0 }}</dd>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
            <dt class="text-sm font-medium text-gray-500">{{ __('Active Volunteers') }}</dt>
            <dd class="mt-2 text-2xl font-bold text-green-600">{{ $activeVolunteers ?? 0 }}</dd>
        </div>
    </div>
</div>
