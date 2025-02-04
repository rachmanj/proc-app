<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'pr_draft_no',
        'pr_no',
        'pr_date',
        'generated_date',
        'priority',
        'pr_status',
        'closed_status',
        'pr_rev_no',
        'pr_type',
        'project_code',
        'dept_name',
        'for_unit',
        'hours_meter',
        'required_date',
        'requestor',
        'remarks'
    ];

    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }
}
