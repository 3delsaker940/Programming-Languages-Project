<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function updateStatus(Request $request, User $user)
{
    $request->validate([
        'status' => 'required|in:active,pending,rejected,frozen',
    ]);

    $user->status = $request->status;
    $user->save();

    return response()->json([
        'message' => 'Status updated successfully',
        'status' => $user->status
    ]);
}
}
