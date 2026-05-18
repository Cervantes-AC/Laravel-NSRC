<div class="space-y-6" aria-label="{{ __('Rankings') }}">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 p-8 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white">{{ __('Volunteer Rankings') }}</h1>
                    <p class="text-white/80 text-sm font-medium">{{ __('Top performers leaderboard') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <select wire:model.live="period" class="bg-white/15 backdrop-blur border-0 text-white text-sm rounded-xl px-3 py-1.5 font-medium">
                    <option value="all" class="text-gray-900">{{ __('All Time') }}</option>
                    <option value="this_week" class="text-gray-900">{{ __('This Week') }}</option>
                    <option value="this_month" class="text-gray-900">{{ __('This Month') }}</option>
                </select>
                <button wire:click="toggleScoringGuide" class="px-3 py-1.5 bg-white/15 backdrop-blur hover:bg-white/25 text-white text-sm font-medium rounded-xl transition">
                    {{ __('Scoring Guide') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Scoring Guide --}}
    @if($showScoringGuide)
        <div class="bg-white border border-amber-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-black text-gray-900">{{ __('How Scoring Works') }}</h3>
                <button wire:click="toggleScoringGuide" class="p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="text-2xl mb-1">🌱</div>
                    <p class="text-sm font-black text-green-800">Beginner</p>
                    <p class="text-xs text-green-600">&lt; 10 hours</p>
                </div>
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="text-2xl mb-1">💪</div>
                    <p class="text-sm font-black text-blue-800">Rising</p>
                    <p class="text-xs text-blue-600">10 – 24 hours</p>
                </div>
                <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl">
                    <div class="text-2xl mb-1">🔥</div>
                    <p class="text-sm font-black text-orange-800">Dedicated</p>
                    <p class="text-xs text-orange-600">25 – 49 hours</p>
                </div>
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="text-2xl mb-1">⭐</div>
                    <p class="text-sm font-black text-amber-800">Veteran</p>
                    <p class="text-xs text-amber-600">50 – 99 hours</p>
                </div>
            </div>
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-center">
                <div class="text-3xl mb-1">🏆</div>
                <p class="text-sm font-black text-yellow-800">Century Club</p>
                <p class="text-xs text-yellow-600">100+ hours — Top achiever status</p>
            </div>
        </div>
    @endif

    {{-- Search --}}
    <div class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search by name...') }}" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-orange-500/30 focus:border-orange-400 outline-none transition-all shadow-sm" aria-label="{{ __('Search rankings') }}" />
    </div>

    {{-- Podium --}}
    @if(count($topThree) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            @php $podiumOrder = [1, 0, 2]; @endphp
            @foreach($podiumOrder as $pos)
                @php
                    $entry = $topThree[$pos] ?? null;
                    if (!$entry) continue;
                    $rank = $pos + 1;
                    $totalMin = $entry['total_minutes'] ?? $entry->total_minutes ?? 0;
                    $name = $entry['full_name'] ?? $entry->full_name ?? 'Unknown';
                    $sessions = $entry['session_count'] ?? $entry->session_count ?? 0;
                    $achievements = $this->getAchievements($totalMin);
                    $achievement = $achievements[0] ?? ['label' => '', 'icon' => '', 'color' => ''];
                @endphp
                <div class="relative bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all text-center {{ $rank === 1 ? 'sm:mb-0 ring-2 ring-yellow-400 ring-offset-2' : ($rank === 2 ? 'sm:mb-4' : 'sm:mb-8') }}">
                    {{-- Rank badge --}}
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-black shadow-lg
                            {{ $rank === 1 ? 'bg-yellow-400 text-yellow-900' : ($rank === 2 ? 'bg-gray-300 text-gray-700' : 'bg-orange-300 text-orange-900') }}">
                            {{ $rank === 1 ? '1st' : ($rank === 2 ? '2nd' : '3rd') }}
                        </span>
                    </div>
                    {{-- Avatar --}}
                    <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center font-black text-xl border-2 shadow-sm mt-2
                        {{ $rank === 1 ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : ($rank === 2 ? 'bg-gray-100 text-gray-700 border-gray-300' : 'bg-orange-100 text-orange-800 border-orange-300') }}">
                        {{ substr($name, 0, 2) }}
                    </div>
                    <h3 class="mt-3 text-base font-black text-gray-900 truncate">{{ $name }}</h3>
                    <p class="text-xs text-gray-500 font-medium">{{ $sessions }} sessions</p>
                    {{-- Stats --}}
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="p-2 bg-gray-50 rounded-lg">
                            <p class="text-sm font-black text-gray-900">{{ floor($totalMin / 60) }}h {{ $totalMin % 60 }}m</p>
                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Total</p>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg">
                            <p class="text-sm font-black text-gray-900">{{ $entry['session_count'] ?? $entry->session_count ?? 0 }}</p>
                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Sessions</p>
                        </div>
                    </div>
                    {{-- Achievement --}}
                    <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $achievement['color'] }} bg-gray-50 border border-gray-200">
                        <span>{{ $achievement['icon'] }}</span>
                        {{ $achievement['label'] }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Rankings Table --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-base font-black text-gray-900">{{ __('Leaderboard') }}</h3>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $rankings->count() }} volunteers</span>
                <select wire:model.live="sortBy" class="text-sm border-gray-200 rounded-lg shadow-sm focus:border-orange-400 focus:ring-orange-400">
                    <option value="total_hours">{{ __('Total Hours') }}</option>
                    <option value="total_sessions">{{ __('Total Sessions') }}</option>
                    <option value="avg_duration">{{ __('Regular Hours') }}</option>
                    <option value="compliance">{{ __('Compliance') }}</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Rank') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Achievement') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Total Hours') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Regular') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Overtime') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wider">{{ __('Sessions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rankings ?? [] as $index => $entry)
                        @php
                            $rank = $loop->iteration;
                            $totalMin = $entry['total_minutes'] ?? $entry->total_minutes ?? 0;
                            $name = $entry['full_name'] ?? $entry->full_name ?? 'Unknown';
                            $achievements = $this->getAchievements($totalMin);
                            $achievement = $achievements[0] ?? ['label' => '', 'icon' => '', 'color' => ''];
                            $isPodium = $rank <= 3;
                            $isCurrentUser = false;
                        @endphp
                        <tr class="{{ $isPodium ? 'bg-amber-50/50' : 'hover:bg-gray-50' }} transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-800 font-black text-sm">🥇</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-800 font-black text-sm">🥈</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-800 font-black text-sm">🥉</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-gray-600 font-bold text-sm">{{ $rank }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black {{ $isPodium ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ substr($name, 0, 2) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $achievement['color'] }} bg-white border border-gray-200">
                                    <span>{{ $achievement['icon'] }}</span>
                                    {{ $achievement['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-gray-900">{{ floor($totalMin / 60) }}h {{ $totalMin % 60 }}m</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ floor(($entry['total_regular_minutes'] ?? $entry->total_regular_minutes ?? 0) / 60) }}h {{ ($entry['total_regular_minutes'] ?? $entry->total_regular_minutes ?? 0) % 60 }}m</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600 font-medium">{{ floor(($entry['total_overtime_minutes'] ?? $entry->total_overtime_minutes ?? 0) / 60) }}h {{ ($entry['total_overtime_minutes'] ?? $entry->total_overtime_minutes ?? 0) % 60 }}m</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $entry['session_count'] ?? $entry->session_count ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No ranking data available yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
