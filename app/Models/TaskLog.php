<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'report_type_id',
        'facility_id',
        'work_date',
        'started_at',
        'ended_at',
        'duration_minutes',
        'status',
        'notes',
        'created_by',
        'last_edited_by',
        'time_edited_at',
    ];

    protected $casts = [
        'work_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'time_edited_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportType()
    {
        return $this->belongsTo(ReportType::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastEditedBy()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * Recalculate duration_minutes from started_at/ended_at.
     * Called whenever either timestamp changes (stop action or admin edit).
     */
    public function recalculateDuration(): void
    {
        if ($this->started_at && $this->ended_at) {
            $minutes = $this->started_at->diffInMinutes($this->ended_at);
            $this->duration_minutes = max(0, $minutes);
        } else {
            $this->duration_minutes = null;
        }
    }

    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_minutes === null) {
            return '—';
        }

        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        }
        if ($hours > 0) {
            return "{$hours}h";
        }

        return "{$minutes}m";
    }

    public function scopeForWorkDate($query, $date)
    {
        return $query->whereDate('work_date', Carbon::parse($date)->format('Y-m-d'));
    }

    /**
     * Scope: logs matching the given work_date, OR currently running
     * regardless of which date they started on. Used by dashboards so an
     * overnight task doesn't disappear from "today" once midnight passes.
     */
    public function scopeForWorkDateOrRunning($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $query->where(function ($q) use ($date) {
            $q->whereDate('work_date', $date)
              ->orWhere('status', self::STATUS_RUNNING);
        });
    }
}
