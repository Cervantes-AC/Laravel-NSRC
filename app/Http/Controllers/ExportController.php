<?php

namespace App\Http\Controllers;

use App\Models\DutySession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function index()
    {
        return view('admin.export.index');
    }

    public function accounts(Request $request)
    {
        $users = User::all();

        $filename = 'accounts-export-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Status', 'Registered']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->full_name ?? $user->name,
                    $user->email,
                    $user->role,
                    $user->status,
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function sessions(Request $request)
    {
        $query = DutySession::query()->with('volunteer');

        if ($request->dateFrom) {
            $query->whereDate('date', '>=', $request->dateFrom);
        }
        if ($request->dateTo) {
            $query->whereDate('date', '<=', $request->dateTo);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderByDesc('date')->get();

        $filename = 'sessions-export-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($sessions) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, ['ID', 'Name', 'Date', 'Time In', 'Time Out', 'Duration (min)', 'Status', 'Location', 'Sector', 'Integrity Score']);

            foreach ($sessions as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->full_name,
                    $s->date?->format('Y-m-d'),
                    $s->time_in?->format('H:i:s'),
                    $s->time_out?->format('H:i:s'),
                    $s->duration_minutes,
                    $s->status,
                    $s->location,
                    $s->sector,
                    $s->integrity_score,
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function personnel(Request $request)
    {
        $users = User::where('role', '!=', 'admin')->with('dutySessions')->get();

        $filename = 'personnel-export-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w+');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Total Sessions', 'Total Minutes']);

            foreach ($users as $u) {
                fputcsv($handle, [
                    $u->id,
                    $u->full_name ?? $u->name,
                    $u->email,
                    $u->role,
                    $u->dutySessions->count(),
                    $u->dutySessions->sum('duration_minutes'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
