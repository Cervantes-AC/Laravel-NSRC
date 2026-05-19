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
        $this->provider = (string) session('ai_provider', config('ai.provider', 'groq'));
        $this->keyIndex = (int) session('ai_key_index', 1);
        $this->setApiKey();
    }

    private function setApiKey(): void
    {
        $config = $this->providerConfig();
        $this->apiKey = $this->keyIndex === 1
            ? (string) ($config['api_key_1'] ?? '')
            : (string) ($config['api_key_2'] ?? '');

        if ($this->apiKey === '') {
            $envKey = strtoupper($this->provider).'_API_KEY_'.$this->keyIndex;
            throw new Exception("API key not configured: {$envKey}");
        }
    }

    /**
     * @return array{api_key_1?: string, api_key_2?: string, endpoint?: string, model?: string}
     */
    private function providerConfig(): array
    {
        return config("ai.{$this->provider}", []);
    }

    public function switchProvider(string $provider): self
    {
        $validProviders = ['groq', 'openrouter'];

        if (! in_array($provider, $validProviders, true)) {
            throw new Exception('Invalid provider: '.$provider.'. Valid options: '.implode(', ', $validProviders));
        }

        $this->provider = $provider;
        $this->keyIndex = 1;
        session(['ai_provider' => $provider, 'ai_key_index' => 1]);
        $this->setApiKey();

        return $this;
    }

    public function switchApiKey(): self
    {
        $this->keyIndex = $this->keyIndex === 1 ? 2 : 1;
        session(['ai_key_index' => $this->keyIndex]);
        $this->setApiKey();

        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getKeyIndex(): int
    {
        return $this->keyIndex;
    }

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
            if ($this->keyIndex === 1) {
                $this->switchApiKey();

                return $this->generateReportInsightsWithoutRetry($reportData, $reportType);
            }

            throw $e;
        }
    }

    private function generateReportInsightsWithoutRetry(array $reportData, string $reportType): string
    {
        $prompt = $this->buildPrompt($reportData, $reportType);

        return match ($this->provider) {
            'groq' => $this->callGroqAPI($prompt),
            'openrouter' => $this->callOpenRouterAPI($prompt),
            default => throw new Exception("Unknown provider: {$this->provider}"),
        };
    }

    private function callGroqAPI(string $prompt): string
    {
        $config = $this->providerConfig();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($config['endpoint'] ?? 'https://api.groq.com/openai/v1/chat/completions', [
            'model' => $config['model'] ?? 'llama-3.3-70b-versatile',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => (float) config('ai.temperature', 0.7),
            'max_tokens' => (int) config('ai.max_tokens', 1024),
        ]);

        if ($response->failed()) {
            throw new Exception('Groq API error: '.$response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    private function callOpenRouterAPI(string $prompt): string
    {
        $config = $this->providerConfig();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name', 'NSRC AMS'),
        ])->timeout(60)->post($config['endpoint'] ?? 'https://openrouter.ai/api/v1/chat/completions', [
            'model' => $config['model'] ?? 'mistralai/mistral-7b-instruct',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => (float) config('ai.temperature', 0.7),
            'max_tokens' => (int) config('ai.max_tokens', 1024),
        ]);

        if ($response->failed()) {
            throw new Exception('OpenRouter API error: '.$response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

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
