<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ItemService extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function po_service()
    {
        return $this->belongsTo(PoService::class);
    }

    protected static function boot()
    {
        parent::boot();
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->username;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->username;
            }
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->username;
            }
        });

    }
}
