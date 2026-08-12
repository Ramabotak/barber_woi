<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    // Dipakai bersama oleh customer, barber, dan admin.
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->orderByDesc('created_at')->paginate(20);

        $layout = match ($request->user()->role) {
            'admin' => 'layouts.admin',
            'barber' => 'layouts.barber',
            default => 'layouts.customer',
        };

        return view('notifications.index', compact('notifications', 'layout'));
    }

    // Dipoll berkala dari browser (lihat resources/js/app.js) untuk badge & toast pop-up.
    public function latest(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'is_read' => $notification->is_read,
            ]);

        return response()->json([
            'unread_count' => $request->user()->notifications()->unread()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, int $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return back();
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back();
    }
}
