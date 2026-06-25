<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachingLog extends Model
{
    protected $fillable = [
        'member_id', 'created_by', 'coaching_type', 'week_number',
        'session_date',
        'root_cause_coachee', 'coach_comments', 'tools',
        'coachee_action_plan', 'coach_commitment', 'duration',
        'targets', 'employee_combined_text', 'client_classification',
        'acknowledged_at',
    ];

    protected $casts = [
        'session_date'    => 'date',
        'acknowledged_at' => 'datetime',
    ];

    public function member() { return $this->belongsTo(User::class, 'member_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
