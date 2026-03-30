<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseRequest extends Model
{
    use LogsActivity;

    protected $fillable = [
        'pr_draft_no',
        'pr_no',
        'pr_date',
        'generated_date',
        'day',
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

    protected $casts = [
        'pr_date' => 'date',
        'generated_date' => 'date',
        'required_date' => 'date',
    ];

    protected $appends = ['day_difference'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['pr_status', 'pr_no', 'pr_draft_no', 'requestor', 'dept_name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Purchase Request " . ($this->pr_no ?? $this->pr_draft_no) . " has been {$eventName}");
    }

    public function getDayDifferenceAttribute()
    {
        // If pr_status is approved, stop calculating and return stored day value
        if ($this->pr_status === 'approved') {
            return $this->day; // Return the stored day value from database
        }

        if (!$this->generated_date) {
            return null;
        }

        $generatedDate = Carbon::parse($this->generated_date);
        $today = Carbon::today();

        return $generatedDate->diffInDays($today);
    }

    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(PrAttachment::class, 'pr_attachment_purchase_request')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function assignments()
    {
        return $this->hasMany(PrAssignment::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'pr_assignments', 'purchase_request_id', 'assigned_to_user_id')
            ->withPivot('notes', 'assigned_by_user_id', 'created_at')
            ->withTimestamps();
    }

    public function follows()
    {
        return $this->hasMany(PrFollow::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'pr_follows', 'purchase_request_id', 'user_id')
            ->withTimestamps();
    }
}
