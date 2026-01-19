<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Apartment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


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

    public function show($id)
    {
        $user = User::with(['apartments', 'Reservation'])->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'birthdate' => $user->birthdate,
            'number' => $user->number,
            'status' => $user->status,
            'type' => $user->type,
            'id_photo_front' => $user->id_photo_front ? url("api/user/id-photo/front/" . $user->id) : null,

            'id_photo_back' => $user->id_photo_back ? url("api/user/id-photo/back/" . $user->id) : null,
            'apartments' => $user->apartments,
            'reservations' => Schema::hasTable('reservations') ? $user->Reservation : [],
        ]);
    }

    public function deleteUser(User $user)
    {
        $user->delete(); // هذا سيقوم بتنفيذ الـ deleting hook في الـ Model

        return response()->json([
            'message' => 'User deleted'
        ], 200);
    }

    public function destroy(Apartment $apartment)
    {

        $user = $apartment->user->id;
        $path = "apartments/{$user}/{$apartment->id}";

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->deleteDirectory($path);
        }

        $apartment->delete();

        return response()->json(['message' => 'Apartment deleted']);
    }

}
