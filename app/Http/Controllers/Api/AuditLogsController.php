<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog as AuditLogModel;
use App\Services\DataExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $dateFrom = $request->input('dateFrom', '');
        $dateTo = $request->input('dateTo', '');
        $perPage = (int) $request->input('perPage', 50);
        $page = (int) $request->input('page', 1);

        $query = AuditLogModel::with('user')->whereNull('archived_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%');
            });
        }
        if ($type) { $query->where('type', $type); }
        if ($dateFrom) { $query->whereDate('created_at', '>=', $dateFrom); }
        if ($dateTo) { $query->whereDate('created_at', '<=', $dateTo); }

        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        $logs = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)->take($perPage)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'created_at' => $log->created_at->format('M d, Y h:i A'),
                'user' => $log->user->full_name ?? 'System',
                'type' => $log->type,
                'action' => $log->action,
                'details' => $log->details ?? 'N/A',
                'ip_address' => $log->ip_address ?? 'N/A',
            ]);

        return response()->json([
            'logs' => $logs,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'total' => $total,
        ]);
    }

    public function export(Request $request, DataExportService $exportService): JsonResponse
    {
        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $dateFrom = $request->input('dateFrom', '');
        $dateTo = $request->input('dateTo', '');

        $query = AuditLogModel::with('user')->whereNull('archived_at')->orderByDesc('created_at');
        if ($search) { $query->where(function ($q) use ($search) { $q->where('full_name', 'like', '%' . $search . '%')->orWhere('action', 'like', '%' . $search . '%'); }); }
        if ($type) { $query->where('type', $type); }
        if ($dateFrom) { $query->whereDate('created_at', '>=', $dateFrom); }
        if ($dateTo) { $query->whereDate('created_at', '<=', $dateTo); }

        $logs = $query->get();
        $exportService->exportToCSV($logs, 'audit_log_' . now()->format('Ymd_His'));

        return response()->json(['message' => 'Export started']);
    }
}
