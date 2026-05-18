<div class="space-y-6" aria-label="{{ __('Analytics') }}" x-data="{
    initChart() {
        const canvas = this.$refs.chartCanvas;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        canvas.style.width = rect.width + 'px';
        canvas.style.height = rect.height + 'px';
        ctx.scale(dpr, dpr);
        this.drawChart(ctx, rect.width, rect.height);
    },
    drawChart(ctx, w, h) {
        const labels = {{ json_encode($chartData['labels'] ?? []) }};
        const sessions = {{ json_encode($chartData['datasets'][0]['data'] ?? []) }};
        const hours = {{ json_encode($chartData['datasets'][1]['data'] ?? []) }};

        if (!labels.length) {
            ctx.fillStyle = '#9CA3AF';
            ctx.font = '14px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('No data available', w / 2, h / 2);
            return;
        }

        const padding = { top: 20, right: 20, bottom: 40, left: 50 };
        const chartW = w - padding.left - padding.right;
        const chartH = h - padding.top - padding.bottom;

        const maxSessions = Math.max(...sessions, 1);
        const barWidth = Math.min(chartW / labels.length * 0.35, 30);
        const gap = chartW / labels.length;

        // Grid lines
        ctx.strokeStyle = '#F3F4F6';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 4; i++) {
            const y = padding.top + (chartH / 4) * i;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(w - padding.right, y);
            ctx.stroke();
        }

        // Bars
        labels.forEach((label, i) => {
            const x = padding.left + gap * i + (gap - barWidth) / 2;
            const barH = (sessions[i] / maxSessions) * chartH;
            const y = padding.top + chartH - barH;

            // Gradient
            const grad = ctx.createLinearGradient(x, y, x, padding.top + chartH);
            grad.addColorStop(0, '#F97316');
            grad.addColorStop(1, '#EF4444');
            ctx.fillStyle = grad;

            ctx.beginPath();
            ctx.roundRect(x, y, barWidth, barH, [3, 3, 0, 0]);
            ctx.fill();

            // Label
            ctx.fillStyle = '#6B7280';
            ctx.font = '10px Inter, sans-serif';
            ctx.textAlign = 'center';
            const displayLabel = label.length > 7 ? label.substring(0, 7) + '…' : label;
            ctx.fillText(displayLabel, x + barWidth / 2, padding.top + chartH + 16);

            // Value
            ctx.fillStyle = '#374151';
            ctx.font = 'bold 10px Inter, sans-serif';
            ctx.fillText(sessions[i], x + barWidth / 2, y - 4);
        });
    }
}" x-init="initChart(); Livewire.hook('morph.updated', () => setTimeout(() => initChart(), 0))">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-cyan-500 via-blue-500 to-indigo-600 p-8 shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white">{{ __('Analytics') }}</h1>
                    <p class="text-white/80 text-sm font-medium">{{ __('Data insights and trends') }}</p>
                </div>
            </div>
            <div class="flex gap-1 bg-white/10 rounded-xl p-1 flex-wrap">
                @foreach(['week' => 'Week', 'month' => 'Month', '3m' => '3M', '6m' => '6M', 'year' => 'Year', 'all' => 'All'] as $key => $label)
                    <button wire:click="filter('{{ $key }}')" class="px-3 py-1.5 text-sm font-semibold rounded-lg transition {{ $period === $key ? 'bg-white text-indigo-700 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label for="analytics-from" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('From') }}</label>
                <input id="analytics-from" type="date" wire:model.live="dateFrom" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
            </div>
            <div>
                <label for="analytics-to" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('To') }}</label>
                <input id="analytics-to" type="date" wire:model.live="dateTo" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm" />
            </div>
            <div>
                <label for="analytics-status" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Status') }}</label>
                <select id="analytics-status" wire:model.live="status" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="COMPLETE">{{ __('Complete') }}</option>
                    <option value="ONGOING">{{ __('Ongoing') }}</option>
                    <option value="MISSING_TIMEOUT">{{ __('Missing Timeout') }}</option>
                    <option value="INVALID_LOG">{{ __('Invalid Log') }}</option>
                </select>
            </div>
            <div>
                <label for="analytics-sector" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1.5">{{ __('Sector') }}</label>
                <select id="analytics-sector" wire:model.live="sector" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-sm">
                    <option value="">{{ __('All Sectors') }}</option>
                    @foreach($sectors as $sec)
                        <option value="{{ $sec }}">{{ $sec }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="clearFilters" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                    {{ __('Clear Filters') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Sessions') }}</p>
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <p class="text-3xl font-black text-gray-900 leading-none">{{ $totalSessions }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('All time') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Hours') }}</p>
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="text-3xl font-black text-indigo-600 leading-none">{{ number_format($totalHours / 60, 1) }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Volunteer hours logged') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Active Volunteers') }}</p>
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" /></svg>
            </div>
            <p class="text-3xl font-black text-green-600 leading-none">{{ $activeVolunteers }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('With at least 1 session') }}</p>
        </div>
    </div>

    {{-- Chart + Insights --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Session Trends') }}</h3>
            <div class="h-64 relative" wire:key="chart-{{ md5(json_encode($chartData)) }}">
                <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Quick Insights') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Peak Day') }}</p>
                            <p class="text-sm font-black text-gray-900">{{ $insights['peak_day'] ?? 'N/A' }} <span class="font-normal text-gray-500">({{ $insights['peak_day_count'] ?? 0 }} sessions)</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Avg / Volunteer') }}</p>
                            <p class="text-sm font-black text-gray-900">{{ $insights['avg_hours_per_volunteer'] ?? 0 }} <span class="font-normal text-gray-500">hours</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Total Hours') }}</p>
                            <p class="text-sm font-black text-gray-900">{{ $insights['total_hours_rounded'] ?? 0 }} <span class="font-normal text-gray-500">hours logged</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ __('Efficiency') }}</p>
                            <p class="text-sm font-black text-gray-900">{{ $insights['efficiency'] ?? 0 }}% <span class="font-normal text-gray-500">completion rate</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Distribution Mini --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-black text-gray-900 mb-4">{{ __('Status Breakdown') }}</h3>
                @php $statusTotal = array_sum($sessionsByStatus) ?: 1; @endphp
                <div class="space-y-2.5">
                    @foreach(['COMPLETE', 'ONGOING', 'MISSING_TIMEOUT', 'INVALID_LOG'] as $status)
                        @php
                            $count = $sessionsByStatus[$status] ?? 0;
                            $pct = round(($count / $statusTotal) * 100);
                            $color = match($status) {
                                'COMPLETE' => 'bg-green-500',
                                'ONGOING' => 'bg-blue-500',
                                'MISSING_TIMEOUT' => 'bg-amber-500',
                                'INVALID_LOG' => 'bg-red-500',
                                default => 'bg-gray-500',
                            };
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-0.5">
                                <span class="font-semibold text-gray-600">{{ ucfirst(strtolower(str_replace('_', ' ', $status))) }}</span>
                                <span class="font-bold text-gray-900">{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
