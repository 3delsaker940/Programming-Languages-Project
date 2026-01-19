<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApartmentResource;
use App\Models\User;
use App\Models\Apartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function ToggleFavorite(Request $request)
    {
        $request->validate([
            'apartmentId' => 'required|integer|exists:apartments,id',
        ]);
        $apartmentId = $request->apartmentId;
        Apartment::findOrFail($apartmentId);
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isFavorite = $user->favoritesApartment()->where('apartment_id', $apartmentId)->exists();
        if ($isFavorite) {
            $user->favoritesApartment()->detach([$apartmentId]);
            return response()->json([
                'message' => 'Isn\'t favorite'
            ], 200);
        } else {
            $user->favoritesApartment()->syncWithoutDetaching([$apartmentId]);
            return response()->json([
                'message' => 'Is favourite'
            ], 200);
        }
    }
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'apartmentId' => 'required|integer|exists:apartments,id',
        ]);
        $apartmentId = $request->apartmentId;
        Apartment::findOrFail($apartmentId);
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->favoritesApartment()->syncWithoutDetaching([$apartmentId]);
        return response()->json([
            'message' => 'Apartment added to favorites successfully'
        ], 200);
    }
    public function removeFromFavorites(Request $request)
    {
        $request->validate([
            'apartmentId' => 'required|integer|exists:apartments,id',
        ]);
        $apartmentId = $request->apartmentId;
        Apartment::findOrFail($apartmentId);
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->favoritesApartment()->detach([$apartmentId]);
        return response()->json([
            'message' => 'Apartment removed from favorites successfully'
        ], 200);
    }
    public function getFavorites()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $favorites = $user->favoritesApartment()->get();
        return ApartmentResource::collection($favorites);
    }
    public function checkIfFavorite(Request $request)
    {
        $request->validate([
            'apartmentId' => 'required|integer|exists:apartments,id',
        ]);
        $apartmentId = $request->apartmentId;
        Apartment::findOrFail($apartmentId);
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isFavorite = $user->favoritesApartment()->where('apartment_id', $apartmentId)->exists();
        return response()->json(['is_favorite' => $isFavorite], 200);
    }

    //=============================================== Admin ======================================

    public function ChangeUserStatusToActive($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'active';
        $user->save();
        return response()->json([
            'message' => 'User status changed successfully'
        ], 200);
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
