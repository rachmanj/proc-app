<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function index(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $lineItemId = $request->input('line_item_id');
        
        $query = $model->comments()
            ->with(['user', 'replies.user', 'mentions.user', 'attachments'])
            ->whereNull('parent_id'); // Only top-level comments
        
        if ($lineItemId) {
            $query->where('line_item_id', $lineItemId);
        } else {
            $query->whereNull('line_item_id'); // Header comments only
        }
        
        $comments = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    public function getLineItemComments(Request $request, $type, $id, $lineItemId)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $comments = Comment::where('commentable_type', get_class($model))
            ->where('commentable_id', $model->id)
            ->where('line_item_id', $lineItemId)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'mentions.user', 'attachments'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    public function getCommentCounts(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        // Get header comment count
        $headerCount = Comment::where('commentable_type', get_class($model))
            ->where('commentable_id', $model->id)
            ->whereNull('line_item_id')
            ->whereNull('parent_id')
            ->count();

        // Get line item comment counts
        $lineItemCounts = Comment::where('commentable_type', get_class($model))
            ->where('commentable_id', $model->id)
            ->whereNotNull('line_item_id')
            ->whereNull('parent_id')
            ->selectRaw('line_item_id, COUNT(*) as count')
            ->groupBy('line_item_id')
            ->pluck('count', 'line_item_id');

        return response()->json([
            'header' => $headerCount,
            'line_items' => $lineItemCounts
        ]);
    }

    public function store(Request $request, $type, $id)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
            'line_item_id' => 'nullable|integer',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max
        ]);

        $model = $this->getModel($type, $id);

        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $content = $request->input('content');
        $contentPlain = strip_tags($content);

        $comment = Comment::create([
            'commentable_type' => get_class($model),
            'commentable_id' => $model->id,
            'parent_id' => $request->input('parent_id'),
            'line_item_id' => $request->input('line_item_id'),
            'user_id' => Auth::id(),
            'content' => $content,
            'content_plain' => $contentPlain,
        ]);

        if ($request->hasFile('attachments')) {
            $this->handleAttachments($comment, $request->file('attachments'));
        }

        $this->handleMentions($comment, $content);

        $comment->load(['user', 'replies', 'mentions.user', 'attachments']);

        // Log activity
        ActivityService::logComment($model, $comment, $request->input('line_item_id'));

        return response()->json($comment, 201);
    }

    public function show($id)
    {
        $comment = Comment::with(['user', 'replies.user', 'mentions.user', 'attachments'])
            ->findOrFail($id);

        return response()->json($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $content = $request->input('content');
        $contentPlain = strip_tags($content);

        // Remove old mentions
        $comment->mentions()->delete();

        $comment->update([
            'content' => $content,
            'content_plain' => $contentPlain,
        ]);

        // Re-parse mentions
        $this->handleMentions($comment, $content);

        $comment->load(['user', 'replies', 'mentions.user', 'attachments']);

        return response()->json($comment);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    public function toggleResolve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->is_resolved = !$comment->is_resolved;
        $comment->save();

        return response()->json($comment);
    }

    public function togglePin($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->is_pinned = !$comment->is_pinned;
        $comment->save();

        return response()->json($comment);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('q', '');
        $forAssignment = $request->input('for_assignment', false);

        $usersQuery = \App\Models\User::where('is_active', true);

        // If searching for assignment, filter by buyer role
        if ($forAssignment) {
            $usersQuery->whereHas('roles', function ($q) {
                $q->where('name', 'buyer');
            });
        }

        $users = $usersQuery->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'username', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    protected function getModel($type, $id)
    {
        if ($type === 'pr' || $type === 'purchase-request') {
            return PurchaseRequest::find($id);
        } elseif ($type === 'po' || $type === 'purchase-order') {
            return PurchaseOrder::find($id);
        }

        return null;
    }

    protected function handleAttachments($comment, $files)
    {
        foreach ($files as $file) {
            $path = $file->store('comment-attachments', 'public');

            $comment->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }

    protected function handleMentions($comment, $content)
    {
        // Extract text from HTML and find @mentions
        $plainText = strip_tags($content);
        preg_match_all('/@(\w+)/', $plainText, $matches);

        if (!empty($matches[1])) {
            $usernames = array_unique($matches[1]);

            $users = \App\Models\User::where('is_active', true)
                ->whereIn('username', $usernames)
                ->get();

            foreach ($users as $user) {
                // Check if mention already exists to avoid duplicates
                $exists = $comment->mentions()
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$exists) {
                    $comment->mentions()->create([
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
