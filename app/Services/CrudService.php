<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrudService
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function create(string $modelClass, array $data, array $auditInfo): Model
    {
        $data['lock_version'] = 1;

        $model = DB::transaction(function () use ($modelClass, $data) {
            return $modelClass::create($data);
        });

        $this->logAudit(array_merge($auditInfo, [
            'action' => $auditInfo['action'],
            'details' => $auditInfo['details'] ?? $this->defaultCreateDetails($model),
        ]));

        return $model;
    }

    public function update(Model $model, array $data, array $auditInfo): bool
    {
        $changes = $this->getChangedAttributes($model, $data);
        $data['lock_version'] = DB::raw('lock_version + 1');

        $updated = DB::transaction(function () use ($model, $data) {
            return $model->update($data);
        });

        if ($updated) {
            $details = $auditInfo['details'] ?? $this->defaultUpdateDetails($model, $changes);
            $this->logAudit(array_merge($auditInfo, [
                'action' => $auditInfo['action'],
                'details' => $details . $this->formatChanges($changes),
            ]));
        }

        return $updated;
    }

    public function delete(Model $model, array $auditInfo, bool $isBulk = false): bool
    {
        $cascadeWarning = $this->getCascadeWarning($model);

        $model->delete();

        $details = $auditInfo['details'] ?? $this->defaultDeleteDetails($model);
        if ($isBulk) {
            $details .= ' [BULK]';
        }
        if ($cascadeWarning) {
            $details .= " | Cascade: {$cascadeWarning}";
        }

        $this->logAudit(array_merge($auditInfo, [
            'action' => $auditInfo['action'],
            'details' => $details,
        ]));

        $this->notifyAdmins($auditInfo, $details, $isBulk);

        return true;
    }

    public function restore(string $modelClass, int $id, array $auditInfo): ?Model
    {
        $model = $modelClass::withTrashed()->findOrFail($id);
        $model->restore();

        $details = $auditInfo['details'] ?? $this->defaultRestoreDetails($model);

        $this->logAudit(array_merge($auditInfo, [
            'action' => $auditInfo['action'] ?? 'RESTORE',
            'details' => $details,
        ]));

        return $model;
    }

    public function getCascadeWarning(Model $model): ?string
    {
        $relations = [];
        $modelClass = get_class($model);
        $modelId = $model->id;

        $relationMap = [
            User::class => [
                'duty sessions' => fn () => $model->dutySessions()->count(),
                'audit logs' => fn () => $model->auditLogs()->count(),
                'notifications' => fn () => $model->notifications()->count(),
            ],
        ];

        if (isset($relationMap[$modelClass])) {
            foreach ($relationMap[$modelClass] as $label => $counter) {
                $count = $counter();
                if ($count > 0) {
                    $relations[] = "{$count} {$label}";
                }
            }
        }

        return empty($relations) ? null : 'This will also affect: ' . implode(', ', $relations) . '.';
    }

    public function getChangedAttributes(Model $model, array $data): array
    {
        $changes = [];
        foreach ($data as $key => $value) {
            if ($key === 'lock_version' || $key === 'password') {
                continue;
            }
            $original = $model->getOriginal($key);
            if ((string) $original !== (string) $value) {
                $changes[$key] = [
                    'old' => $original,
                    'new' => $value,
                ];
            }
        }
        return $changes;
    }

    public function hasConflict(Model $model, int $submittedLockVersion): bool
    {
        return (int) $model->lock_version > $submittedLockVersion;
    }

    private function logAudit(array $auditInfo): void
    {
        $request = request();

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => $auditInfo['type'] ?? 'OPERATIONS',
            'action' => $auditInfo['action'],
            'details' => $auditInfo['details'] ?? '',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);
    }

    private function notifyAdmins(array $auditInfo, string $details, bool $isBulk): void
    {
        $action = $auditInfo['action'] ?? 'OPERATION';
        $title = str_replace('_', ' ', $action);
        $level = $isBulk ? 'warning' : 'info';

        $this->notificationService->notifyAdmins(
            'crud',
            ucwords(strtolower($title)),
            $details,
            ['level' => $level]
        );
    }

    private function defaultCreateDetails(Model $model): string
    {
        return class_basename($model) . " #{$model->id} created";
    }

    private function defaultUpdateDetails(Model $model, array $changes): string
    {
        return class_basename($model) . " #{$model->id} updated";
    }

    private function defaultDeleteDetails(Model $model): string
    {
        return class_basename($model) . " #{$model->id} soft deleted (" . ($model->full_name ?? $model->title ?? $model->name ?? 'N/A') . ')';
    }

    private function defaultRestoreDetails(Model $model): string
    {
        return class_basename($model) . " #{$model->id} restored (" . ($model->full_name ?? $model->title ?? $model->name ?? 'N/A') . ')';
    }

    private function formatChanges(array $changes): string
    {
        if (empty($changes)) {
            return '';
        }
        $parts = [];
        foreach ($changes as $field => $vals) {
            $old = is_scalar($vals['old']) ? $vals['old'] : json_encode($vals['old']);
            $new = is_scalar($vals['new']) ? $vals['new'] : json_encode($vals['new']);
            $parts[] = "{$field}: '{$old}' → '{$new}'";
        }
        return ' | Changes: ' . implode(', ', $parts);
    }
}
