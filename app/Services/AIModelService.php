<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AIModelService
{
    /**
     * Available AI models with their configurations
     */
    private const AVAILABLE_MODELS = [
        'claude-3-5-sonnet' => [
            'name' => 'Claude 3.5 Sonnet',
            'provider' => 'Anthropic',
            'description' => 'Most capable model, best for complex tasks',
            'icon' => 'sparkles',
            'tier' => 'premium',
        ],
        'claude-3-opus' => [
            'name' => 'Claude 3 Opus',
            'provider' => 'Anthropic',
            'description' => 'Powerful model for advanced reasoning',
            'icon' => 'brain',
            'tier' => 'premium',
        ],
        'claude-3-sonnet' => [
            'name' => 'Claude 3 Sonnet',
            'provider' => 'Anthropic',
            'description' => 'Balanced performance and speed',
            'icon' => 'zap',
            'tier' => 'standard',
        ],
        'claude-3-haiku' => [
            'name' => 'Claude 3 Haiku',
            'provider' => 'Anthropic',
            'description' => 'Fast and efficient for quick tasks',
            'icon' => 'feather',
            'tier' => 'standard',
        ],
        'gpt-4-turbo' => [
            'name' => 'GPT-4 Turbo',
            'provider' => 'OpenAI',
            'description' => 'Advanced reasoning and analysis',
            'icon' => 'cpu',
            'tier' => 'premium',
        ],
        'gpt-4' => [
            'name' => 'GPT-4',
            'provider' => 'OpenAI',
            'description' => 'Powerful general-purpose model',
            'icon' => 'cpu',
            'tier' => 'premium',
        ],
        'gpt-3.5-turbo' => [
            'name' => 'GPT-3.5 Turbo',
            'provider' => 'OpenAI',
            'description' => 'Fast and cost-effective',
            'icon' => 'zap',
            'tier' => 'standard',
        ],
        'gemini-pro' => [
            'name' => 'Gemini Pro',
            'provider' => 'Google',
            'description' => 'Multimodal AI model',
            'icon' => 'sparkles',
            'tier' => 'standard',
        ],
        'llama-2-70b' => [
            'name' => 'Llama 2 70B',
            'provider' => 'Meta',
            'description' => 'Open-source large language model',
            'icon' => 'code',
            'tier' => 'standard',
        ],
    ];

    /**
     * Get all available models
     */
    public function getAvailableModels(): array
    {
        return self::AVAILABLE_MODELS;
    }

    /**
     * Get model by ID
     */
    public function getModel(string $modelId): ?array
    {
        return self::AVAILABLE_MODELS[$modelId] ?? null;
    }

    /**
     * Get user's selected model
     */
    public function getUserModel(): string
    {
        $user = Auth::user();
        if (! $user) {
            return 'claude-3-5-sonnet'; // Default model
        }

        return $user->preferences?->ai_model ?? 'claude-3-5-sonnet';
    }

    /**
     * Set user's selected model
     */
    public function setUserModel(string $modelId): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        // Validate model exists
        if (! isset(self::AVAILABLE_MODELS[$modelId])) {
            return false;
        }

        // Get or create user preferences
        $preferences = $user->preferences ?? $user->preferences()->create();

        return $preferences->update(['ai_model' => $modelId]);
    }

    /**
     * Get models grouped by provider
     */
    public function getModelsByProvider(): array
    {
        $grouped = [];

        foreach (self::AVAILABLE_MODELS as $id => $model) {
            $provider = $model['provider'];
            if (! isset($grouped[$provider])) {
                $grouped[$provider] = [];
            }
            $grouped[$provider][$id] = $model;
        }

        return $grouped;
    }

    /**
     * Get models by tier
     */
    public function getModelsByTier(string $tier): array
    {
        return array_filter(
            self::AVAILABLE_MODELS,
            fn ($model) => $model['tier'] === $tier
        );
    }

    /**
     * Get model configuration for API calls
     */
    public function getModelConfig(string $modelId): array
    {
        $model = $this->getModel($modelId);

        if (! $model) {
            return [];
        }

        return [
            'id' => $modelId,
            'name' => $model['name'],
            'provider' => $model['provider'],
            'tier' => $model['tier'],
        ];
    }
}
