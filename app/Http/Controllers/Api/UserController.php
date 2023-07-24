<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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

}
