<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'default_days',
        'is_paid',
        'description',
        'is_active',
    ];

    protected $casts = [
        'default_days' => 'integer',
        'is_paid'      => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
