<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\DataExportService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(
        protected DataExportService $exportService
    ) {}

    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(15);

        return view('admin.audit-logs.index', compact('logs'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $data = $query->latest()->get();
        $filename = 'audit-logs-' . now()->format('Y-m-d');

        return $this->exportService->exportToCSV($data, $filename);
    }
}
