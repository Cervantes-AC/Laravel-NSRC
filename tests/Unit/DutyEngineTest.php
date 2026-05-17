<?php

namespace Tests\Unit;

use App\Services\DutyEngine;
use App\Services\NameNormalizationService;
use PHPUnit\Framework\TestCase;

class DutyEngineTest extends TestCase
{
    private DutyEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new DutyEngine(new NameNormalizationService);
    }

    public function test_calculate_duration_returns_minutes(): void
    {
        $timeIn = now()->parse('2026-05-18 08:00:00');
        $timeOut = now()->parse('2026-05-18 12:30:00');

        $duration = $this->engine->calculateDuration($timeIn, $timeOut);

        $this->assertEquals(270, $duration);
    }

    public function test_calculate_duration_returns_zero_when_timeout_before_timein(): void
    {
        $timeIn = now()->parse('2026-05-18 14:00:00');
        $timeOut = now()->parse('2026-05-18 08:00:00');

        $duration = $this->engine->calculateDuration($timeIn, $timeOut);

        $this->assertEquals(0, $duration);
    }

    public function test_calculate_duration_returns_zero_when_missing_time(): void
    {
        $this->assertEquals(0, $this->engine->calculateDuration(null, now()));
        $this->assertEquals(0, $this->engine->calculateDuration(now(), null));
        $this->assertEquals(0, $this->engine->calculateDuration(null, null));
    }

    public function test_assign_sector_matches_location(): void
    {
        $this->assertEquals('Security', $this->engine->assignSector('Main Gate'));
        $this->assertEquals('Administration', $this->engine->assignSector('Admin Office'));
        $this->assertEquals('Health Services', $this->engine->assignSector('Clinic'));
        $this->assertEquals('Academics', $this->engine->assignSector('Library'));
        $this->assertEquals('Events', $this->engine->assignSector('Auditorium'));
        $this->assertEquals('Logistics', $this->engine->assignSector('Cafeteria'));
    }

    public function test_assign_sector_returns_general_for_unknown(): void
    {
        $this->assertEquals('General', $this->engine->assignSector('Parking Lot'));
        $this->assertEquals('General', $this->engine->assignSector(''));
    }

    public function test_generate_integrity_score(): void
    {
        $this->assertEquals(100.0, $this->engine->generateIntegrityScore(now(), now()));
        $this->assertEquals(60.0, $this->engine->generateIntegrityScore(now(), null));
        $this->assertEquals(40.0, $this->engine->generateIntegrityScore(null, now()));
        $this->assertEquals(40.0, $this->engine->generateIntegrityScore(null, null));
    }

    public function test_determine_status(): void
    {
        $this->assertEquals('COMPLETE', $this->engine->determineStatus(now(), now(), true));
        $this->assertEquals('ONGOING', $this->engine->determineStatus(now(), null, true));
        $this->assertEquals('MISSING_TIMEOUT', $this->engine->determineStatus(now(), null, false));
        $this->assertEquals('INVALID_LOG', $this->engine->determineStatus(null, now(), false));
    }

    public function test_process_empty_logs_returns_empty(): void
    {
        $result = $this->engine->processDutyLogs(collect());
        $this->assertCount(0, $result);
    }
}
