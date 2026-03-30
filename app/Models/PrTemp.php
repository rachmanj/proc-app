<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrTemp extends Model
{
    protected $fillable = [
        'sap_doc_entry',
        'sap_line_num',
        'sap_vis_order',
        'line_identity',
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
        'item_code',
        'item_name',
        'quantity',
        'uom',
        'open_qty',
        'line_remarks',
        'remarks'
    ];
}
