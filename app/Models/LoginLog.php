<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'work_date',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'work_date'  => 'date',
        'login_at'   => 'datetime',
        'logout_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** True if still logged in (no logout_at yet). */
    public function isOnShift(): bool
    {
        return is_null($this->logout_at);
    }

    /**
     * Total shift duration in minutes.
     * For an open shift, counts up to now.
     */
    public function shiftDurationMinutes(): int
    {
        $end = $this->logout_at ?? Carbon::now();
        return (int) $this->login_at->diffInMinutes($end);
    }

    /** Scope: open shifts (no logout yet). */
    public function scopeOpen($query)
    {
        return $query->whereNull('logout_at');
    }

    /** Scope: shifts for a given date. */
    public function scopeForDate($query, string $date)
    {
        return $query->where('work_date', $date);
    }
}