<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = $user->notifications()->orderByDesc('created_at');

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by severity
        if ($request->has('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by read status
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $user->notifications()->whereNull('read_at')->count(),
            'critical' => $user->notifications()->where('severity', 'critical')->count(),
            'warnings' => $user->notifications()->where('severity', 'warning')->count(),
            'unacknowledged' => $user->notifications()->whereNull('acknowledged_at')->count(),
        ];

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Display failure notifications only
     */
    public function failures(Request $request): View
    {
        $user = auth()->user();

        $query = $user->notifications()
            ->whereIn('type', [
                'failure_notification',
                'batch_failure_notification',
                'critical_alert',
                'warning_alert',
            ])
            ->orderByDesc('created_at');

        // Filter by severity
        if ($request->has('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $notifications = $query->paginate(20);

        $stats = [
            'total_failures' => $user->notifications()->failures()->count(),
            'critical_failures' => $user->notifications()->failures()->where('severity', 'critical')->count(),
            'warning_failures' => $user->notifications()->failures()->where('severity', 'warning')->count(),
            'unacknowledged_failures' => $user->notifications()->failures()->whereNull('acknowledged_at')->count(),
        ];

        return view('notifications.failures', compact('notifications', 'stats'));
    }

    /**
     * Display a single notification
     */
    public function show(Notification $notification): View
    {
        $this->authorize('view', $notification);

        // Mark as read
        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        $this->authorize('update', $notification);

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Acknowledge a notification
     */
    public function acknowledge(Notification $notification): RedirectResponse
    {
        $this->authorize('update', $notification);

        $notification->acknowledge(auth()->user()->email);

        return back()->with('success', 'Notification acknowledged');
    }

    /**
     * Acknowledge all critical notifications
     */
    public function acknowledgeAllCritical(): RedirectResponse
    {
        auth()->user()->notifications()
            ->where('severity', 'critical')
            ->whereNull('acknowledged_at')
            ->update([
                'acknowledged_at' => now(),
                'acknowledged_by' => auth()->user()->email,
            ]);

        return back()->with('success', 'All critical notifications acknowledged');
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return back()->with('success', 'Notification deleted');
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead(): RedirectResponse
    {
        auth()->user()->notifications()
            ->whereNotNull('read_at')
            ->delete();

        return back()->with('success', 'All read notifications deleted');
    }

    /**
     * Get unread notification count (for AJAX)
     */
    public function unreadCount()
    {
        $count = auth()->user()->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent failures (for dashboard widget)
     */
    public function recentFailures()
    {
        $failures = auth()->user()->notifications()
            ->failures()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json($failures);
    }

    /**
     * Get critical notifications (for alerts)
     */
    public function criticalAlerts()
    {
        $alerts = auth()->user()->notifications()
            ->where('severity', 'critical')
            ->whereNull('acknowledged_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($alerts);
    }

    /**
     * Export notifications to CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();

        $query = $user->notifications()->orderByDesc('created_at');

        // Apply filters
        if ($request->has('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $notifications = $query->get();

        $filename = 'notifications_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($notifications) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Date',
                'Type',
                'Severity',
                'Category',
                'Message',
                'Reason',
                'Read At',
                'Acknowledged At',
            ]);

            foreach ($notifications as $notification) {
                fputcsv($file, [
                    $notification->created_at->format('Y-m-d H:i:s'),
                    $notification->type,
                    $notification->severity,
                    $notification->category,
                    $notification->data['message'] ?? '',
                    $notification->failure_reason ?? '',
                    $notification->read_at?->format('Y-m-d H:i:s') ?? '',
                    $notification->acknowledged_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
