<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalLevel extends Model
{
    protected $table = 'approval_levels';

    protected $fillable = [
        'name',
        'level'
    ];

    public function approvals()
    {
        return $this->hasMany(PurchaseOrderApproval::class);
    }

    public function approvers()
    {
        return $this->hasMany(Approver::class);
    }
} 