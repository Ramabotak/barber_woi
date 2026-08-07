<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    // Dipakai bersama oleh customer, barber, dan admin.
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->orderByDesc('created_at')->paginate(20);

        return view('notifications.index', compact('notifications'));
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
