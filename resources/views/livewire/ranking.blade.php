<div class="space-y-4" aria-label="{{ __('Rankings') }}">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Volunteer Rankings') }}</h3>
        <div>
            <label for="sort-by" class="sr-only">{{ __('Sort by') }}</label>
            <select id="sort-by" wire:model="sortBy" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Sort rankings by') }}">
                <option value="total_hours">{{ __('Total Hours') }}</option>
                <option value="total_sessions">{{ __('Total Sessions') }}</option>
                <option value="avg_duration">{{ __('Avg Duration') }}</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Rankings table') }}">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Rank') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Hours') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Sessions') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Avg Duration') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rankings ?? [] as $index => $entry)
                    @php $rank = $loop->iteration; @endphp
                    <tr class="{{ $rank <= 3 ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($rank === 1)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-800 font-bold text-sm" aria-label="{{ __('Rank') }} 1">&#127942;</span>
                            @elseif($rank === 2)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-800 font-bold text-sm" aria-label="{{ __('Rank') }} 2">&#129352;</span>
                            @elseif($rank === 3)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-800 font-bold text-sm" aria-label="{{ __('Rank') }} 3">&#129353;</span>
                            @else
                                <span class="inline-flex items-center justify-center w-8 h-8 text-gray-600 font-bold text-sm">{{ $rank }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $rank <= 3 ? 'text-gray-900' : 'text-gray-900' }}">{{ $entry['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry['total_hours'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry['total_sessions'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry['avg_duration'] }} {{ __('min') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No ranking data available yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
