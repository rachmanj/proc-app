<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $filters = [
            'event' => $request->input('event'),
            'user_id' => $request->input('user_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $activities = ActivityService::getActivitiesForSubject($model, array_filter($filters));

        return response()->json($activities);
    }

    public function getEvents(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $events = Activity::where('subject_type', get_class($model))
            ->where('subject_id', $model->id)
            ->distinct()
            ->pluck('event')
            ->filter()
            ->values();

        return response()->json($events);
    }

    public function getUsers(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        $userIds = Activity::where('subject_type', get_class($model))
            ->where('subject_id', $model->id)
            ->distinct()
            ->pluck('causer_id')
            ->filter();

        $users = \App\Models\User::whereIn('id', $userIds)
            ->select('id', 'name', 'username')
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
}
