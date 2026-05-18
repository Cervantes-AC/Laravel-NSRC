<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $announcements = Announcement::with('creator')
            ->when($request->input('search'), fn ($query, $search) => $query
                ->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('body', 'like', "%{$search}%")))
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('admin.announcements.create', [
            'announcement' => new Announcement([
                'priority' => 'normal',
                'status' => 'draft',
                'audience' => 'members',
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $announcement = Announcement::create($this->validated($request) + [
            'created_by' => Auth::id(),
        ]);

        $this->notifyMembersIfPublished($announcement);
        $this->audit('CREATE_ANNOUNCEMENT', $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update($this->validated($request));

        $this->notifyMembersIfPublished($announcement);
        $this->audit('UPDATE_ANNOUNCEMENT', $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->audit('DELETE_ANNOUNCEMENT', $announcement);
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:normal,important,urgent'],
            'status' => ['required', 'in:draft,published,archived'],
            'audience' => ['required', 'in:members,all'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:published_at'],
        ]);
    }

    private function notifyMembersIfPublished(Announcement $announcement): void
    {
        if ($announcement->status !== 'published' || $announcement->notified_at || $announcement->published_at?->isFuture()) {
            return;
        }

        User::where('status', 'active')
            ->where('role', 'member')
            ->chunkById(100, function ($members) use ($announcement): void {
                foreach ($members as $member) {
                    $member->notifications()->create([
                        'id' => (string) Str::uuid(),
                        'type' => 'announcement',
                        'data' => [
                            'announcement_id' => $announcement->id,
                            'title' => $announcement->title,
                            'message' => Str::limit($announcement->body, 180),
                            'priority' => $announcement->priority,
                            'type' => 'announcement',
                        ],
                    ]);
                }
            });

        $announcement->forceFill(['notified_at' => now()])->save();
    }

    private function audit(string $action, Announcement $announcement): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'full_name' => Auth::user()?->full_name ?? Auth::user()?->name ?? 'Admin',
            'type' => 'OPERATIONS',
            'action' => $action,
            'details' => "Announcement #{$announcement->id}: {$announcement->title}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
