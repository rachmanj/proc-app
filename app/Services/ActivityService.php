<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    public static function logComment($subject, $comment, $lineItemId = null)
    {
        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties([
                'comment_id' => $comment->id,
                'line_item_id' => $lineItemId,
                'content_preview' => substr(strip_tags($comment->content), 0, 100),
            ])
            ->event('commented')
            ->log('commented on ' . class_basename($subject));
    }

    public static function logFileUpload($subject, $attachment, $type = 'attachment')
    {
        $fileName = $attachment->original_name ?? null;
        $fileType = $attachment->file_type ?? pathinfo($attachment->file_path ?? '', PATHINFO_EXTENSION);
        $fileSize = $attachment->file_size ?? null;

        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties([
                'attachment_id' => $attachment->id,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
            ])
            ->event('file_uploaded')
            ->log('uploaded file to ' . class_basename($subject));
    }

    public static function logFileDeleted($subject, $attachment)
    {
        $fileName = $attachment->original_name ?? null;

        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties([
                'file_name' => $fileName,
            ])
            ->event('file_deleted')
            ->log('deleted file from ' . class_basename($subject));
    }

    public static function logStatusChange($subject, $oldStatus, $newStatus)
    {
        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ])
            ->event('status_changed')
            ->log('changed status from ' . $oldStatus . ' to ' . $newStatus);
    }

    public static function logAssignment($subject, $assignedTo, $notes = null)
    {
        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties([
                'assigned_to' => $assignedTo->id,
                'assigned_to_name' => $assignedTo->name,
                'notes' => $notes,
            ])
            ->event('assigned')
            ->log('assigned to ' . $assignedTo->name);
    }

    public static function logApproval($subject, $action, $notes = null, $level = null)
    {
        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->withProperties([
                'action' => $action, // approved, rejected, revision
                'notes' => $notes,
                'approval_level' => $level,
            ])
            ->event('approval_' . $action)
            ->log($action . ' ' . class_basename($subject));
    }

    public static function logFollow($subject, $action = 'followed')
    {
        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->event('followed')
            ->log($action . ' ' . class_basename($subject));
    }

    public static function logUnfollow($subject)
    {
        activity()
            ->performedOn($subject)
            ->causedBy(Auth::user())
            ->event('unfollowed')
            ->log('unfollowed ' . class_basename($subject));
    }

    public static function getActivitiesForSubject($subject, $filters = [])
    {
        $query = Activity::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->with('causer')
            ->orderBy('created_at', 'desc');

        if (isset($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (isset($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    public static function getLastActivity($subject)
    {
        return Activity::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
