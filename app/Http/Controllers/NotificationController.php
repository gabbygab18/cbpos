<?php

namespace App\Http\Controllers;

use App\Models\CoachingLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a single notification as read, and — if it's a coaching log
     * notification — sync coaching_logs.acknowledged_at as well.
     */
    public function markRead(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);

            $this->syncAcknowledgedAt($notification);
        }

        return back();
    }

    /**
     * Mark all of the current user's unread notifications as read,
     * syncing acknowledged_at for any related coaching logs.
     */
    public function markAllRead()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->get();

        foreach ($notifications as $notification) {
            $notification->update(['read_at' => now()]);
            $this->syncAcknowledgedAt($notification);
        }

        return back();
    }

    /**
     * If this notification is tied to a CoachingLog, stamp
     * coaching_logs.acknowledged_at to match — but only the first time.
     */
    protected function syncAcknowledgedAt(Notification $notification): void
    {
        if ($notification->notifiable_type !== CoachingLog::class) {
            return;
        }

        CoachingLog::where('id', $notification->notifiable_id)
            ->whereNull('acknowledged_at')
            ->update(['acknowledged_at' => $notification->read_at]);
    }
}
