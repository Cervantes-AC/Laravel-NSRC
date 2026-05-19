<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use App\Models\DutySession;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimisticLockingMiddleware
{
    private array $modelMap = [
        'UserController' => User::class,
        'AccountsController' => User::class,
        'PersonnelController' => User::class,
        'UserManagementController' => User::class,
        'AnnouncementController' => Announcement::class,
        'SessionsController' => DutySession::class,
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $lockVersion = $request->input('lock_version');
            $modelId = $request->route('id')
                ?? $request->route('user')
                ?? $request->route('announcement')
                ?? $request->route('session');

            if ($lockVersion !== null && $modelId !== null) {
                $modelClass = $this->resolveModel($request);
                if ($modelClass) {
                    $record = $modelClass::find($modelId);
                    if ($record && (int) $record->lock_version > (int) $lockVersion) {
                        if ($request->wantsJson()) {
                            return response()->json([
                                'message' => 'This record was modified by another user. Please refresh and try again.',
                                'error' => 'optimistic_lock_conflict',
                            ], 409);
                        }

                        return back()->withErrors([
                            'lock_version' => 'This record was modified by another user. Please refresh and try again.',
                        ])->withInput();
                    }
                }
            }
        }

        return $next($request);
    }

    protected function resolveModel(Request $request): ?string
    {
        $route = $request->route();
        if (! $route) {
            return null;
        }

        $controller = $route->getControllerClass();

        foreach ($this->modelMap as $key => $class) {
            if (str_contains($controller ?? '', $key)) {
                return $class;
            }
        }

        return null;
    }
}
