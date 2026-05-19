<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class MemberAnnouncementsController extends Controller
{
    public function recent(): JsonResponse
    {
        $announcements = Announcement::visibleToMembers()
            ->latest('published_at')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'body' => $a->body,
                'priority' => $a->priority,
                'published_at' => $a->published_at?->diffForHumans(),
            ]);

        return response()->json([
            'announcements' => $announcements,
            'total' => Announcement::visibleToMembers()->count(),
        ]);
    }
}
