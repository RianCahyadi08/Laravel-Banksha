<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show()
    {
        $user = getUser(auth()->user()->id);

        return response()->json($user);
    }

    public function getUserByUsername(Request $request, $username)
    {
        $user = User::select(
            'id', 'name', 'username', 'verified', 'profile_picture'
        )->where('username', 'LIKE', '%'.$username.'%')->where('id', '<>', auth()->user()->id)->get();

        $user->map(function ($item) {
            $item->profile_picture = $item->profile_picture ? url('storage/'.$item->profile_picture) : '';
        
            return $item;
        });

        return response()->json($user);
    }

    public function update(Request $request) 
    {
        try {
            $user = User::findOrFail(auth()->user()->id);
            $data = $request->only('name', 'username', 'ktp', 'email', 'password');

            if ($request->username != $user->username) {
                $isExistUsername = User::where('username', $request->username)->exists();
                if ($isExistUsername) {
                    return response()->json([
                        'message' => 'Username already taken',
                    ]);
                }
            }

            if ($request->email != $user->email) {
                $isExistEmail = User::where('email', $request->email)->exists();
                if ($isExistEmail) {
                    return response()->json([
                        'message' => 'Email already taken'
                    ]);
                }
            }

            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            }

            if ($request->profile_picture) {
                $profilePicture = uploadBase64Image($request->profile_picture);
                $data['profile_picture'] = $profilePicture;
                if ($user->profile_picture) {
                    Storage::delete('public/'.$user->profile_picture);
                }
            }

            if ($request->ktp) {
                $ktpPicture = uploadBase64Image($request->ktp);
                $data['ktp'] = $ktpPicture;
                $data['verified'] = true;
                if ($user->ktp) {
                    Storage::delete('public/'.$user->ktp);
                }
            }

            $user->update($data);

            return response()->json([
                'message' => 'Successfully updated data',
            ]);
        } catch (\Throwable $th) {
            echo $th;
        }
        
    }

}
