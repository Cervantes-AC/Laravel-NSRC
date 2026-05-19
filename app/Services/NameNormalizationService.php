<?php

namespace App\Services;

use App\Models\DutySession;
use Illuminate\Support\Collection;

class NameNormalizationService
{
    public function normalizeName(string $name): array
    {
        $name = trim($name);

        if (str_contains($name, ',')) {
            $parts = array_map('trim', explode(',', $name, 2));
        } else {
            $parts = preg_split('/\s+/', $name);
        }

        $parts = array_filter($parts, fn ($p) => $p !== '');
        $parts = array_values($parts);

        sort($parts);

        return $parts;
    }

    public function calculateSimilarity(string $name1, string $name2): float
    {
        $normalized1 = $this->normalizeName($name1);
        $normalized2 = $this->normalizeName($name2);

        $str1 = strtolower(implode(' ', $normalized1));
        $str2 = strtolower(implode(' ', $normalized2));

        $lev = levenshtein($str1, $str2);
        $maxLen = max(mb_strlen($str1), mb_strlen($str2));

        if ($maxLen === 0) {
            return 100.0;
        }

        return round((1 - $lev / $maxLen) * 100, 2);
    }

    public function areNamesSimilar(string $name1, string $name2, float $threshold = 85.0): bool
    {
        return $this->calculateSimilarity($name1, $name2) >= $threshold;
    }

    public function getCanonicalName(Collection $sessions, string $name, float $threshold = 50.0): string
    {
        $bestMatch = $name;
        $highestScore = 0.0;

        foreach ($sessions as $session) {
            $sessionName = $session instanceof DutySession
                ? $session->full_name
                : ($session['full_name'] ?? '');

            if (empty($sessionName)) {
                continue;
            }

            $score = $this->calculateSimilarity($name, $sessionName);

            if ($score > $highestScore) {
                $highestScore = $score;
            }

            if ($score >= $threshold) {
                $bestMatch = $sessionName;
                break;
            }
        }

        return $bestMatch;
    }
}
