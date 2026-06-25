<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngagementRecord extends Model
{
    const TYPES = [
        'team_meeting'     => 'Team Meeting Attendance',
        'feedback'         => 'Employee Feedback',
        'recognition'      => 'Recognition / Awards',
        'suggestion'       => 'Suggestion for Improvement',
        'kpi'              => 'Monthly KPI',
        'scorecard'        => 'Individual Performance Scorecard',
        'coaching_session' => 'Coaching Session',
        'pip'              => 'Performance Improvement Plan',
    ];

    protected $fillable = [
        'member_id', 'created_by', 'record_type', 'title', 'notes', 'score', 'record_date',
    ];

    protected $casts = ['record_date' => 'date', 'score' => 'float'];

    public function member() { return $this->belongsTo(User::class, 'member_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->record_type] ?? $this->record_type;
    }
}
