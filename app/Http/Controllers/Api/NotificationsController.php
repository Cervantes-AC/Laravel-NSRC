<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unreadCount' => 0]);
        }

        $notifications = $user->notifications()->latest()->take(50)->get()->map(fn ($n) => [
            'id' => $n->id,
            'data' => $n->data,
            'type' => class_basename($n->type),
            'title' => $n->data['title'] ?? $n->data['subject'] ?? class_basename($n->type),
            'message' => $n->data['message'] ?? $n->data['body'] ?? $n->data['description'] ?? '',
            'read_at' => $n->read_at,
            'created_at' => $n->created_at->diffForHumans(),
            'kind' => $n->data['type'] ?? $n->data['level'] ?? 'info',
        ]);

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(string $id): JsonResponse
    {
        $notification = DatabaseNotification::find($id);
        if ($notification && (string) $notification->notifiable_id === (string) auth()->id()) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): JsonResponse
    {
        auth()->user()?->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function destroy(string $id): JsonResponse
    {
        $notification = DatabaseNotification::find($id);
        if ($notification && (string) $notification->notifiable_id === (string) auth()->id()) {
            $notification->delete();
        }
        return response()->json(['success' => true]);
    }
}
