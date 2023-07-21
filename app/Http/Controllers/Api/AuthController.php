<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\User;
use App\Models\Wallet;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    
    public function register(Request $request) 
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name'      => 'required|string',
            'email'     => 'required|email',
            'password'  => 'required|string|min:6',
            'pin'       => 'required|digits:6'
        ]);

        if ($validator->fails())
        {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        // var_dump($request->email);
        $user = User::where('email', $request->email)->exists();

        if ($user)
        {
            return response()->json([
                'message'   => 'Email already taken'
            ], 409);
        }

        try {
            $profilePicture = null;
            $ktp            = null;

            if ($request->profile_picture) {
                $profilePicture = $this->uploadBase64Image($request->profile_picture);
            }

            if ($request->ktp) {
                $ktp = $this->uploadBase64Image($request->ktp);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => ''
            ]);
            // echo $th;
        }


    }

    private function uploadBase64Image($base64Image)
    {
        $decoder        = new Base64ImageDecoder($base64Image, $allowedFormats = ['jpeg', 'png', 'gif', 'jpg']);
        $decodedContent = $decoder->getDecodedContent(); 
        $format         = $decoder->getFormat();
        $image          = Str::random(10).'.'.$format;
        Storage::disk('public')->put($image, $decodedContent);
        return $image;
    }

}
