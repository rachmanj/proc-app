<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'parent_id',
        'line_item_id',
        'user_id',
        'content',
        'content_plain',
        'is_resolved',
        'is_pinned',
        'is_deleted',
        'deleted_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'is_pinned' => 'boolean',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function mentions()
    {
        return $this->hasMany(CommentMention::class);
    }

    public function attachments()
    {
        return $this->hasMany(CommentAttachment::class);
    }
}
