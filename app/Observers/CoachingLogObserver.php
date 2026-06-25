<?php

namespace App\Observers;

use App\Models\CoachingLog;
use App\Models\Notification;

class CoachingLogObserver
{
    /**
     * Fire when a new coaching log is created — notify the member.
     */
    public function created(CoachingLog $log): void
    {
        Notification::create([
            'user_id'         => $log->member_id,   // the member being coached
            'type'            => 'coaching_log',
            'title'           => 'New Coaching Log',
            'message'         => "A new {$log->coaching_type} coaching log was added for you"
                                  . ($log->week_number ? " (Week {$log->week_number})" : '') . '.',
            'notifiable_type' => CoachingLog::class,
            'notifiable_id'   => $log->id,
        ]);
    }
}
