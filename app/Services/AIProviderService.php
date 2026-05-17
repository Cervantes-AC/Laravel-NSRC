<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class AIProviderService
{
    private string $provider;
    private string $apiKey;
    private int $keyIndex = 1;

    public function __construct()
    {
        $this->provider = config('app.ai_provider', 'groq');
        $this->setApiKey();
    }

    /**
     * Set the API key based on the current provider and key index
     */
    private function setApiKey(): void
    {
        $envKey = strtoupper($this->provider) . '_API_KEY_' . $this->keyIndex;
        $this->apiKey = env($envKey, '');

        if (empty($this->apiKey)) {
            throw new Exception("API key not configured: {$envKey}");
        }
    }

    /**
     * Switch to a different provider
     */
    public function switchProvider(string $provider): self
    {
        $validProviders = ['groq', 'openrouter'];

        if (!in_array($provider, $validProviders)) {
            throw new Exception("Invalid provider: {$provider}. Valid options: " . implode(', ', $validProviders));
        }

        $this->provider = $provider;
        $this->keyIndex = 1;
        $this->setApiKey();

        return $this;
    }

    /**
     * Switch to the alternate API key for the current provider
     */
    public function switchApiKey(): self
    {
        $this->keyIndex = $this->keyIndex === 1 ? 2 : 1;
        $this->setApiKey();

        return $this;
    }

    /**
     * Get the current provider
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Get the current API key index
     */
    public function getKeyIndex(): int
    {
        return $this->keyIndex;
    }

    /**
     * Generate AI-powered report insights
     */
    public function generateReportInsights(array $reportData, string $reportType): string
    {
        try {
            $prompt = $this->buildPrompt($reportData, $reportType);

            return match ($this->provider) {
                'groq' => $this->callGroqAPI($prompt),
                'openrouter' => $this->callOpenRouterAPI($prompt),
                default => throw new Exception("Unknown provider: {$this->provider}"),
            };
        } catch (Exception $e) {
            // Try alternate API key if available
            if ($this->keyIndex === 1) {
                $this->switchApiKey();
                return $this->generateReportInsights($reportData, $reportType);
            }

            throw $e;
        }
    }

    /**
     * Call Groq API
     */
    private function callGroqAPI(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'mixtral-8x7b-32768',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ]);

        if ($response->failed()) {
            throw new Exception('Groq API error: ' . $response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    /**
     * Call OpenRouter API
     */
    private function callOpenRouterAPI(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'mistralai/mistral-7b-instruct',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ]);

        if ($response->failed()) {
            throw new Exception('OpenRouter API error: ' . $response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    /**
     * Build a prompt for report analysis
     */
    private function buildPrompt(array $reportData, string $reportType): string
    {
        $dataJson = json_encode($reportData, JSON_PRETTY_PRINT);

        return <<<PROMPT
Analyze the following {$reportType} report data and provide key insights, trends, and recommendations:

{$dataJson}

Please provide:
1. Key findings
2. Notable trends
3. Potential issues or concerns
4. Recommendations for improvement

Keep the response concise and actionable.
PROMPT;
    }
}
