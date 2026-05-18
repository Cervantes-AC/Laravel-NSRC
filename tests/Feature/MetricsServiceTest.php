<?php

namespace Tests\Feature;

use App\Models\DutySession;
use App\Models\User;
use App\Services\MetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsServiceTest extends TestCase
{
    use RefreshDatabase;

    private MetricsService $metricsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricsService = app(MetricsService::class);
    }

    public function test_calculate_volunteer_metrics_creates_records(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        DutySession::factory()->count(3)->create([
            'volunteer_id' => $user->id,
            'duration_minutes' => 120,
            'status' => 'COMPLETE',
        ]);

        $sessions = DutySession::where('volunteer_id', $user->id)->get();
        $results = $this->metricsService->calculateVolunteerMetrics($sessions);

        $this->assertCount(1, $results);
        $this->assertEquals($user->id, $results->first()->volunteer_id);
        $this->assertEquals(360, $results->first()->total_regular_minutes);
        $this->assertEquals(360, $results->first()->total_minutes);
        $this->assertEquals(0, $results->first()->invalid_record_count);
        $this->assertEquals(3, $results->first()->session_count);
    }

    public function test_calculate_volunteer_metrics_counts_invalid_logs(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        DutySession::factory()->count(2)->create([
            'volunteer_id' => $user->id,
            'duration_minutes' => 60,
            'status' => 'COMPLETE',
        ]);
        DutySession::factory()->count(1)->create([
            'volunteer_id' => $user->id,
            'duration_minutes' => 0,
            'status' => 'INVALID_LOG',
        ]);

        $sessions = DutySession::where('volunteer_id', $user->id)->get();
        $results = $this->metricsService->calculateVolunteerMetrics($sessions);

        $this->assertEquals(1, $results->first()->invalid_record_count);
    }

    public function test_calculate_volunteer_metrics_handles_overtime(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        DutySession::factory()->create([
            'volunteer_id' => $user->id,
            'duration_minutes' => 600,
            'status' => 'COMPLETE',
        ]);

        $sessions = DutySession::where('volunteer_id', $user->id)->get();
        $results = $this->metricsService->calculateVolunteerMetrics($sessions);

        $this->assertEquals(480, $results->first()->total_regular_minutes);
        $this->assertEquals(120, $results->first()->total_overtime_minutes);
        $this->assertEquals(0, $results->first()->total_undertime_minutes);
        $this->assertEquals(600, $results->first()->total_minutes);
    }

    public function test_calculate_volunteer_metrics_handles_undertime(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        DutySession::factory()->create([
            'volunteer_id' => $user->id,
            'duration_minutes' => 240,
            'status' => 'COMPLETE',
        ]);

        $sessions = DutySession::where('volunteer_id', $user->id)->get();
        $results = $this->metricsService->calculateVolunteerMetrics($sessions);

        $this->assertEquals(240, $results->first()->total_regular_minutes);
        $this->assertEquals(0, $results->first()->total_overtime_minutes);
        $this->assertEquals(240, $results->first()->total_undertime_minutes);
        $this->assertEquals(240, $results->first()->total_minutes);
    }

    public function test_get_system_summary_returns_expected_keys(): void
    {
        User::factory()->count(3)->create();
        $summary = $this->metricsService->getSystemSummary();

        $this->assertArrayHasKey('total_users', $summary);
        $this->assertArrayHasKey('active_sessions', $summary);
        $this->assertArrayHasKey('today_count', $summary);
        $this->assertArrayHasKey('average_duration_minutes', $summary);
        $this->assertGreaterThanOrEqual(3, $summary['total_users']);
    }

    public function test_empty_metrics_returns_empty_collection(): void
    {
        $result = $this->metricsService->calculateVolunteerMetrics(collect());
        $this->assertCount(0, $result);
    }
}
