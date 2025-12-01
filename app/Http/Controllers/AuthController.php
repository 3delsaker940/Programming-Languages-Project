<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthdate' => $request->birthdate,
            'number' => $request->number,
            'password' => Hash::make($request->password),
            'profile_photo' => "hi",
            "id_photo_front" => "haboooooooooooooooush",
            "id_photo_back" => "Medannnnnnnnnnnnnnnnnnnnnnnnni"
        ]);
        $IDFront = $request->file('id_photo_front');
        $IDBack = $request->file('id_photo_back');
        $profileImage = $request->file('profile_photo');
        $filename = $user->id;
        $back = $filename . "back";
        $front = $filename . "front";
        $folder = 'private/Profile_photos';
        $folder1 = 'private/ID_photos';
        $path1 = $IDFront->storeAs($folder1, $front);
        $path2 = $IDBack->storeAs($folder1, $back);
        $path = $profileImage->storeAs($folder, $filename);
        $user->profile_photo = $path;
        $user->id_photo_back = $path2;
        $user->id_photo_front = $path1;
        $user->save();
        return response()->json($user, 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'number' => [
                'required',
                'exists:users,number',
                'string',
                'regex:/^(?:\+9639|09|009639)\d{8}$/'
            ],
            'password' => 'required|string'
        ]);
        if (!Auth::attempt($request->only('number', 'password'))) {
            return response()->json(
                [
                    'message' => 'invalid number or password'
                ],
                401
            );
        }
        $user =  User::where('number', $request->number)->firstOrFail();
        $token = $user->createToken('auth_Token')->plainTextToken;
        return response()->json([
            'message' => 'logged in successfully',
            'User' => $user,
            'Token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logged out successfully'
        ]);
    }
}
