<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request, User $user)
    {
        // ... existing validation and user update code ...

        // Update roles
        $user->syncRoles($request->roles ?? []);

        // Update approval levels
        $existingLevels = $user->approvers->pluck('approval_level_id')->toArray();
        $newLevels = $request->approval_levels ?? [];

        // Remove old approval levels that are not in the new selection
        $user->approvers()->whereNotIn('approval_level_id', $newLevels)->delete();

        // Add new approval levels
        foreach ($newLevels as $levelId) {
            if (!in_array($levelId, $existingLevels)) {
                $user->approvers()->create([
                    'approval_level_id' => $levelId
                ]);
            }
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }
} 