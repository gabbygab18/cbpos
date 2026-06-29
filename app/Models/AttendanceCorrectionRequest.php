<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'login_log_id',
        'work_date',
        'requested_login_at',
        'requested_logout_at',
        'reason',
        'status',
        'reviewed_by',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'work_date'    => 'date',
        'reviewed_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loginLog()
    {
        return $this->belongsTo(LoginLog::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
