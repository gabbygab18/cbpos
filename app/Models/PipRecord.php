<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipRecord extends Model
{
    protected $fillable = [
        'member_id', 'created_by', 'category', 'client_name',
        'team_leader', 'manager', 'offense_occurrence', 'due_date',
        'engagement', 'reinforce', 'areas_for_improvement', 'attachment_path',
    ];

    protected $casts = ['due_date' => 'date'];

    public function member() { return $this->belongsTo(User::class, 'member_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
