<?php

namespace Tests\Feature;

use App\Services\DutyEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DutyEngineProcessLogsTest extends TestCase
{
    use RefreshDatabase;

    private DutyEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(DutyEngine::class);
    }

    public function test_process_logs_pairs_time_in_out(): void
    {
        $logs = collect([
            $this->makeLog('John Doe', 'TIME_IN', '2026-05-18 08:00:00', 'Main Gate'),
            $this->makeLog('John Doe', 'TIME_OUT', '2026-05-18 12:00:00', 'Main Gate'),
        ]);

        $sessions = $this->engine->processDutyLogs($logs);

        $this->assertCount(1, $sessions);
        $session = $sessions->first();
        $this->assertEquals('COMPLETE', $session->status);
        $this->assertEquals(240, $session->duration_minutes);
        $this->assertEquals(100.0, $session->integrity_score);
    }

    public function test_process_logs_with_missing_timeout(): void
    {
        $logs = collect([
            $this->makeLog('Jane Smith', 'TIME_IN', '2026-05-18 08:00:00', 'Clinic'),
        ]);

        $sessions = $this->engine->processDutyLogs($logs);

        $this->assertCount(1, $sessions);
        $session = $sessions->first();
        $this->assertEquals('MISSING_TIMEOUT', $session->status);
        $this->assertEquals(70.0, $session->integrity_score);
    }

    public function test_process_logs_handles_normalized_labels(): void
    {
        $logs = collect([
            $this->makeLog('Bob Wilson', 'time_in', '2026-05-18 09:00:00', 'Admin Office'),
            $this->makeLog('Bob Wilson', 'time_out', '2026-05-18 17:00:00', 'Admin Office'),
        ]);

        $sessions = $this->engine->processDutyLogs($logs);

        $this->assertCount(1, $sessions);
        $this->assertEquals('COMPLETE', $sessions->first()->status);
    }

    public function test_process_logs_with_multiple_timeins(): void
    {
        $logs = collect([
            $this->makeLog('Test User', 'TIME_IN', '2026-05-18 08:00:00', 'Main Gate'),
            $this->makeLog('Test User', 'TIME_IN', '2026-05-18 09:00:00', 'Main Gate'),
            $this->makeLog('Test User', 'TIME_OUT', '2026-05-18 12:00:00', 'Main Gate'),
        ]);

        $sessions = $this->engine->processDutyLogs($logs);

        $this->assertCount(2, $sessions);
        $this->assertEquals('MISSING_TIMEOUT', $sessions->get(0)->status);
        $this->assertEquals('COMPLETE', $sessions->get(1)->status);
    }

    public function test_process_logs_assigns_sector(): void
    {
        $logs = collect([
            $this->makeLog('Sector Test', 'TIME_IN', '2026-05-18 08:00:00', 'Clinic'),
            $this->makeLog('Sector Test', 'TIME_OUT', '2026-05-18 16:00:00', 'Clinic'),
        ]);

        $sessions = $this->engine->processDutyLogs($logs);

        $this->assertEquals('Health Services', $sessions->first()->sector);
    }

    public function test_process_logs_with_different_names_creates_separate_sessions(): void
    {
        $logs = collect([
            $this->makeLog('Alice Wonderland', 'TIME_IN', '2026-05-18 08:00:00', 'Main Gate'),
            $this->makeLog('Alice Wonderland', 'TIME_OUT', '2026-05-18 12:00:00', 'Main Gate'),
            $this->makeLog('Charlie Delta', 'TIME_IN', '2026-05-18 09:00:00', 'Clinic'),
            $this->makeLog('Charlie Delta', 'TIME_OUT', '2026-05-18 13:00:00', 'Clinic'),
        ]);

        $sessions = $this->engine->processDutyLogs($logs);

        $this->assertCount(2, $sessions);
        $this->assertEquals('Alice Wonderland', $sessions->get(0)->full_name);
        $this->assertEquals('Charlie Delta', $sessions->get(1)->full_name);
    }

    private function makeLog(string $name, string $attendance, string $dateTime, string $location): array
    {
        return [
            'full_name' => $name,
            'attendance' => $attendance,
            'date_time' => now()->parse($dateTime),
            'location' => $location,
        ];
    }
}
