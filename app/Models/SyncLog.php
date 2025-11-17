<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_type',
        'start_date',
        'end_date',
        'records_synced',
        'records_created',
        'records_skipped',
        'sync_status',
        'convert_status',
        'error_message',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'records_synced' => 'integer',
        'records_created' => 'integer',
        'records_skipped' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
