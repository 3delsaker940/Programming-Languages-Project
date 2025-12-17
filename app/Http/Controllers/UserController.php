<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function profilePhoto()
    {
        $user = Auth::user();

        if (!$user->profile_photo) {
            return response()->json(['message' => 'No profile photo'], 404);
        }

        if (!Storage::disk('public')->exists($user->profile_photo)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $path = Storage::disk('public')->path($user->profile_photo);

        return response()->file($path);
    }

    public function idPhotoFront($userId)
    {
        $user = User::findOrFail($userId);
        if (!$user->id_photo_front) {
            return response()->json(['message' => 'No front ID photo'], 404);
        }
        if (!Storage::disk('local')->exists($user->id_photo_front)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $path = Storage::disk('local')->path($user->id_photo_front);
        return response()->file($path);
    }

    public function idPhotoBack($userId)
    {
        $user = User::findOrFail($userId);
        if (!$user->id_photo_back) {
            return response()->json(['message' => 'No back ID photo'], 404);
        }
        if (!Storage::disk('local')->exists($user->id_photo_back)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $path = Storage::disk('local')->path($user->id_photo_back);
        return response()->file($path);
    }
}
