<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    protected $fillable = ['user_id', 'approval_level_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvalLevel()
    {
        return $this->belongsTo(ApprovalLevel::class);
    }
} 