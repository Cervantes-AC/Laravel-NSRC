<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\AIModelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIModelController extends Controller
{
    public function __construct(private AIModelService $aiModelService) {}

    /**
     * Get current AI model
     */
    public function current(): JsonResponse
    {
        $currentModel = $this->aiModelService->getUserModel();
        $model = $this->aiModelService->getModel($currentModel);

        return response()->json([
            'success' => true,
            'model_id' => $currentModel,
            'model' => $model,
        ]);
    }

    /**
     * Get all available models
     */
    public function index(): JsonResponse
    {
        $models = $this->aiModelService->getAvailableModels();
        $currentModel = $this->aiModelService->getUserModel();

        return response()->json([
            'success' => true,
            'models' => $models,
            'current_model' => $currentModel,
            'by_provider' => $this->aiModelService->getModelsByProvider(),
        ]);
    }

    /**
     * Update user's AI model
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'model_id' => 'required|string',
        ]);

        $modelId = $request->input('model_id');

        // Validate model exists
        if (! $this->aiModelService->getModel($modelId)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid AI model selected',
            ], 422);
        }

        // Update user's model preference
        if ($this->aiModelService->setUserModel($modelId)) {
            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'full_name' => Auth::user()?->name ?? 'Unknown',
                'type' => 'SYSTEM',
                'action' => 'AI_MODEL_CHANGED',
                'details' => "Changed AI model to {$modelId}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'AI model updated successfully',
                'model_id' => $modelId,
                'model' => $this->aiModelService->getModel($modelId),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update AI model',
        ], 500);
    }

    /**
     * Get models by tier
     */
    public function byTier(string $tier): JsonResponse
    {
        $validTiers = ['standard', 'premium'];

        if (! in_array($tier, $validTiers)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid tier',
            ], 422);
        }

        $models = $this->aiModelService->getModelsByTier($tier);

        return response()->json([
            'success' => true,
            'tier' => $tier,
            'models' => $models,
        ]);
    }

    /**
     * Get models by provider
     */
    public function byProvider(string $provider): JsonResponse
    {
        $allModels = $this->aiModelService->getAvailableModels();
        $models = array_filter(
            $allModels,
            fn ($model) => strtolower($model['provider']) === strtolower($provider)
        );

        if (empty($models)) {
            return response()->json([
                'success' => false,
                'message' => 'No models found for this provider',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'provider' => $provider,
            'models' => $models,
        ]);
    }
}
