<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $IDFront = $request->file('id_photo_path_front');
        $IDBack = $request->file('id_photo_path_back');
        $profileImage = $request->file('profile_photo_path');
        // $request->validate();
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthDate' => $request->birthDate,
            'number' => $request->number,
            'password' => Hash::make($request->password),
            'profile_photo_path' => "hi",
            "id_photo_path_front" => "haboooooooooooooooush",
            "id_photo_path_back" => "Medannnnnnnnnnnnnnnnnnnnnnnnni"
        ]);
        $filename = $user->id;
        $back = $filename . "back";
        $front = $filename . "front";
        $folder = 'private/Profile_photos';
        $folder1 = 'private/ID_photos';
        $path1 = $IDFront->storeAs($folder1, $front);
        $path2 = $IDBack->storeAs($folder1, $back);
        $path = $profileImage->storeAs($folder, $filename);
        $user->profile_photo_path = $path;
        $user->id_photo_path_back = $path2;
        $user->id_photo_path_front = $path1;

        $user->save();
        return response()->json($user, 201);
    }
}
