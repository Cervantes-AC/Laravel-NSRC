<?php

namespace App\Livewire;

use App\Models\DutySession;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Analytics extends Component
{
    public array $chartData = [];

    public string $period = 'monthly';

    public function mount(): void
    {
        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        $groupBy = match ($this->period) {
            'daily' => DB::raw("DATE_FORMAT(date, '%Y-%m-%d')"),
            'weekly' => DB::raw("YEARWEEK(date, 1)"),
            'monthly' => DB::raw("DATE_FORMAT(date, '%Y-%m')"),
            default => DB::raw("DATE_FORMAT(date, '%Y-%m')"),
        };

        $labelExpr = match ($this->period) {
            'daily' => DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as label"),
            'weekly' => DB::raw("CONCAT(YEAR(date), '-W', LPAD(WEEK(date, 1), 2, '0')) as label"),
            'monthly' => DB::raw("DATE_FORMAT(date, '%Y-%m') as label"),
            default => DB::raw("DATE_FORMAT(date, '%Y-%m') as label"),
        };

        $records = DutySession::select($labelExpr, DB::raw('COUNT(*) as count'))
            ->whereNotNull('date')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $this->chartData = [
            'labels' => $records->pluck('label')->toArray(),
            'datasets' => [
                [
                    'label' => 'Sessions',
                    'data' => $records->pluck('count')->toArray(),
                ],
            ],
        ];
    }

    public function filter(string $period): void
    {
        $this->period = $period;
        $this->loadChartData();
    }

    public function render()
    {
        return view('livewire.analytics');
    }
}
