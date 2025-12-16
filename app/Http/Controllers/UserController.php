<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function ChangeUserStatusToActive($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'active';
        $user->save();
        return response()->json([
            'message' => 'User status changed successfully'
        ], 200);
    }
}
