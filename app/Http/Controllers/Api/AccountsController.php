<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AccountApproved;
use App\Mail\AccountRejected;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AccountsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $statusFilter = $request->input('statusFilter', '');
        $roleFilter = $request->input('roleFilter', '');
        $sortBy = $request->input('sortBy', 'created_at');
        $sortDirection = $request->input('sortDirection', 'desc');
        $perPage = (int) $request->input('perPage', 25);
        $page = (int) $request->input('page', 1);

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        $allowedSortColumns = ['full_name', 'email', 'role', 'status', 'created_at', 'last_login_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('created_at');
        }

        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        $accounts = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)->take($perPage)
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'full_name' => $u->full_name,
                'email' => $u->email,
                'role' => $u->role,
                'status' => $u->status,
                'avatar' => $u->avatar,
                'created_at' => $u->created_at->format('M d, Y'),
                'last_login_at' => $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never',
                'email_verified_at' => $u->email_verified_at ? $u->email_verified_at->format('M d, Y') : 'Unverified',
                'two_factor_enabled' => $u->two_factor_enabled ?? false,
            ]);

        return response()->json([
            'accounts' => $accounts,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'total' => $total,
            'stats' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'pending' => User::where('status', 'pending')->count(),
                'suspended' => User::where('status', 'suspended')->count(),
                'rejected' => User::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function approve(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'active']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'APPROVE_ACCOUNT',
            'details' => "Approved account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        if ($user->email_notifications_enabled ?? true) {
            Mail::to($user->email)->send(new AccountApproved($user));
        }

        return response()->json(['success' => true, 'message' => 'Account approved successfully.', 'status' => 'active']);
    }

    public function reject(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $reason = request()->input('reason');
        $user->update(['status' => 'rejected']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'REGISTRY',
            'action' => 'REJECT_ACCOUNT',
            'details' => "Rejected account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        if ($user->email_notifications_enabled ?? true) {
            Mail::to($user->email)->send(new AccountRejected($user, $reason));
        }

        return response()->json(['success' => true, 'message' => 'Account rejected successfully.', 'status' => 'rejected']);
    }

    public function suspend(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $user->update(['status' => 'suspended']);

        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? 'System',
            'type' => 'SECURITY',
            'action' => 'SUSPEND_ACCOUNT',
            'details' => "Suspended account for: {$user->full_name} (ID: {$user->id})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Account suspended successfully.', 'status' => 'suspended']);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|in:approve,suspend,reject',
        ]);

        $status = match ($request->action) {
            'approve' => 'active', 'suspend' => 'suspended', 'reject' => 'rejected'
        };
        User::whereIn('id', $request->ids)->update(['status' => $status]);

        return response()->json(['success' => true, 'message' => 'Bulk action completed successfully.']);
    }
}
