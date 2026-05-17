<?php

namespace Tests\Unit;

use App\Models\DutySession;
use App\Services\NameNormalizationService;
use PHPUnit\Framework\TestCase;

class NameNormalizationServiceTest extends TestCase
{
    private NameNormalizationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NameNormalizationService;
    }

    public function test_normalize_simple_name(): void
    {
        $parts = $this->service->normalizeName('John Doe');
        $this->assertEquals(['Doe', 'John'], $parts);
    }

    public function test_normalize_comma_separated_name(): void
    {
        $parts = $this->service->normalizeName('Doe, John');
        $this->assertEquals(['Doe', 'John'], $parts);
    }

    public function test_normalize_name_with_middle_initial(): void
    {
        $parts = $this->service->normalizeName('John Michael Doe');
        $this->assertEquals(['Doe', 'John', 'Michael'], $parts);
    }

    public function test_normalize_trimmed_name(): void
    {
        $parts = $this->service->normalizeName('  Jane  Smith  ');
        $this->assertEquals(['Jane', 'Smith'], $parts);
    }

    public function test_identical_names_have_100_similarity(): void
    {
        $score = $this->service->calculateSimilarity('John Doe', 'John Doe');
        $this->assertEquals(100.0, $score);
    }

    public function test_similar_names_high_score(): void
    {
        $score = $this->service->calculateSimilarity('John Doe', 'Doe, John');
        $this->assertGreaterThanOrEqual(85, $score);
    }

    public function test_different_names_low_score(): void
    {
        $score = $this->service->calculateSimilarity('John Doe', 'Jane Smith');
        $this->assertLessThan(50, $score);
    }

    public function test_are_names_similar_with_default_threshold(): void
    {
        $this->assertTrue($this->service->areNamesSimilar('John Doe', 'Doe, John'));
        $this->assertFalse($this->service->areNamesSimilar('John Doe', 'Jane Smith'));
    }

    public function test_are_names_similar_with_custom_threshold(): void
    {
        $this->assertTrue($this->service->areNamesSimilar('John Michael Doe', 'John Doe', 50));
        $this->assertFalse($this->service->areNamesSimilar('John Michael Doe', 'John Doe', 90));
    }

    public function test_get_canonical_name_returns_best_match(): void
    {
        $existing = collect([
            new DutySession(['full_name' => 'John Michael Doe']),
        ]);

        $result = $this->service->getCanonicalName($existing, 'Doe, John M.');
        $this->assertEquals('John Michael Doe', $result);
    }

    public function test_get_canonical_name_falls_back_to_input(): void
    {
        $result = $this->service->getCanonicalName(collect(), 'New User');
        $this->assertEquals('New User', $result);
    }

    public function test_empty_names_return_100_similarity(): void
    {
        $score = $this->service->calculateSimilarity('', '');
        $this->assertEquals(100.0, $score);
    }

    public function test_partial_name_match(): void
    {
        $score = $this->service->calculateSimilarity('Michael Brown', 'Michelle Brown');
        $this->assertGreaterThan(70, $score);
    }
}
