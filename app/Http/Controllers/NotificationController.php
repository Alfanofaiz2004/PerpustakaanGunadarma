<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(15)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'action_url' => $this->resolveSafeRelativeUrl($notif->action_url),
                    'is_read' => $notif->is_read,
                    'time_ago' => $notif->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse|RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        $targetUrl = $this->resolveSafeRelativeUrl($notification->action_url);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'action_url' => $targetUrl,
            ]);
        }

        return redirect($targetUrl);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi telah ditandai dibaca.',
        ]);
    }

    protected function resolveSafeRelativeUrl(?string $url): string
    {
        if (empty($url)) {
            return auth()->user()?->isAdmin() ? '/admin/loans' : '/peminjam/loans';
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? (auth()->user()?->isAdmin() ? '/admin/loans' : '/peminjam/loans');
        if (!empty($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }

        return $path;
    }
}
