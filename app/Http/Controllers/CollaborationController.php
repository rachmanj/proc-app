<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PrAssignment;
use App\Models\PoAssignment;
use App\Models\PrFollow;
use App\Models\PoFollow;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaborationController extends Controller
{
    // Assignment methods
    public function assign(Request $request, $type, $id)
    {
        // Check if user has permission to assign documents
        if (!Auth::user()->can('assign_document')) {
            return response()->json(['error' => 'You do not have permission to assign documents'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify the user to be assigned has buyer role
        $assignedUser = \App\Models\User::find($request->user_id);
        if (!$assignedUser || !$assignedUser->hasRole('buyer')) {
            return response()->json(['error' => 'Only users with buyer role can be assigned'], 400);
        }

        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        if ($type === 'pr' || $type === 'purchase-request') {
            $assignment = PrAssignment::updateOrCreate(
                [
                    'purchase_request_id' => $model->id,
                    'assigned_to_user_id' => $request->user_id,
                ],
                [
                    'assigned_by_user_id' => Auth::id(),
                    'notes' => $request->notes,
                ]
            );

            ActivityService::logAssignment($model, $assignedUser, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Request assigned successfully',
                'assignment' => $assignment->load('assignedTo', 'assignedBy'),
            ]);
        } elseif ($type === 'po' || $type === 'purchase-order') {
            $assignment = PoAssignment::updateOrCreate(
                [
                    'purchase_order_id' => $model->id,
                    'assigned_to_user_id' => $request->user_id,
                ],
                [
                    'assigned_by_user_id' => Auth::id(),
                    'notes' => $request->notes,
                ]
            );

            ActivityService::logAssignment($model, $assignedUser, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order assigned successfully',
                'assignment' => $assignment->load('assignedTo', 'assignedBy'),
            ]);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function unassign(Request $request, $type, $id, $userId)
    {
        // Check if user has permission to assign documents
        if (!Auth::user()->can('assign_document')) {
            return response()->json(['error' => 'You do not have permission to unassign documents'], 403);
        }

        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        if ($type === 'pr' || $type === 'purchase-request') {
            PrAssignment::where('purchase_request_id', $model->id)
                ->where('assigned_to_user_id', $userId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment removed successfully',
            ]);
        } elseif ($type === 'po' || $type === 'purchase-order') {
            PoAssignment::where('purchase_order_id', $model->id)
                ->where('assigned_to_user_id', $userId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment removed successfully',
            ]);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function getAssignments($type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $assignments = $model->assignedUsers()->get();

        return response()->json($assignments);
    }

    // Follow methods
    public function follow($type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        if ($type === 'pr' || $type === 'purchase-request') {
            $follow = PrFollow::firstOrCreate([
                'purchase_request_id' => $model->id,
                'user_id' => Auth::id(),
            ]);

            ActivityService::logFollow($model);

            return response()->json([
                'success' => true,
                'message' => 'Following Purchase Request',
                'following' => true,
            ]);
        } elseif ($type === 'po' || $type === 'purchase-order') {
            $follow = PoFollow::firstOrCreate([
                'purchase_order_id' => $model->id,
                'user_id' => Auth::id(),
            ]);

            ActivityService::logFollow($model);

            return response()->json([
                'success' => true,
                'message' => 'Following Purchase Order',
                'following' => true,
            ]);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function unfollow($type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        if ($type === 'pr' || $type === 'purchase-request') {
            PrFollow::where('purchase_request_id', $model->id)
                ->where('user_id', Auth::id())
                ->delete();

            ActivityService::logUnfollow($model);

            return response()->json([
                'success' => true,
                'message' => 'Unfollowed Purchase Request',
                'following' => false,
            ]);
        } elseif ($type === 'po' || $type === 'purchase-order') {
            PoFollow::where('purchase_order_id', $model->id)
                ->where('user_id', Auth::id())
                ->delete();

            ActivityService::logUnfollow($model);

            return response()->json([
                'success' => true,
                'message' => 'Unfollowed Purchase Order',
                'following' => false,
            ]);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function getFollowStatus($type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $isFollowing = false;
        if ($type === 'pr' || $type === 'purchase-request') {
            $isFollowing = PrFollow::where('purchase_request_id', $model->id)
                ->where('user_id', Auth::id())
                ->exists();
        } elseif ($type === 'po' || $type === 'purchase-order') {
            $isFollowing = PoFollow::where('purchase_order_id', $model->id)
                ->where('user_id', Auth::id())
                ->exists();
        }

        return response()->json([
            'following' => $isFollowing,
            'followers_count' => $model->followers()->count(),
        ]);
    }

    public function myWatchlist()
    {
        $user = Auth::user();
        
        // Get followed PRs with recent activity
        $followedPRs = $user->followedPRs()
            ->with(['details', 'assignedUsers'])
            ->orderBy('pr_follows.created_at', 'desc')
            ->get()
            ->map(function ($pr) {
                $pr->type = 'pr';
                $pr->last_activity = \App\Services\ActivityService::getLastActivity($pr);
                return $pr;
            });

        // Get followed POs with recent activity
        $followedPOs = $user->followedPOs()
            ->with(['details', 'assignedUsers'])
            ->orderBy('po_follows.created_at', 'desc')
            ->get()
            ->map(function ($po) {
                $po->type = 'po';
                $po->last_activity = \App\Services\ActivityService::getLastActivity($po);
                return $po;
            });

        // Combine and sort by last activity
        $allFollowed = $followedPRs->merge($followedPOs)
            ->sortByDesc(function ($item) {
                return $item->last_activity ? $item->last_activity->created_at : $item->created_at;
            })
            ->values();

        return view('procurement.watchlist.index', compact('allFollowed', 'followedPRs', 'followedPOs'));
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

    public function getBuyers(Request $request)
    {
        $query = $request->input('q', '');

        $users = \App\Models\User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'buyer');
            })
            ->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'username', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
