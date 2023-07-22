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
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JwtException;

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

        $user = User::where('email', $request->email)->exists();
        
        if ($user)
        {
            return response()->json([
                'message'   => 'Email already taken'
            ], 409);
        }

        DB::beginTransaction();

        try {
            $profilePicture = null;
            $ktp            = null;

            if ($request->profile_picture) {
                $profilePicture = $this->uploadBase64Image($request->profile_picture);
            }

            if ($request->ktp) {
                $ktp = $this->uploadBase64Image($request->ktp);
            }
            
            $user = User::create([
                'name'  => $request->name,
                'email' => $request->email,
                'username' => $request->name,
                'password' => bcrypt($request->password),
                'pin' => $request->pin,
                'profile_picture' => $profilePicture,
                'ktp' => $ktp,
                'verified' => ($ktp) ? true : false,
            ]);

            $wallet = Wallet::create([
                'balance' => 0,
                'pin' => $request->pin,
                'card_number' => $this->generateCardNumber(16),
                'user_id' => $user->id,
            ]);

            DB::commit();

            if ($user) {
                return response()->json([
                    'message'   => 'Successfully created data',
                    'data'      => $user,
                ], 200);
            }

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message'   => $th->getMessage(),
            ], 500);
                // echo $th;
        }


    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages()
            ], 400);
        }

        try {
            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return response()->json([
                    'message' => 'Login credentials are invalid'
                ]);
            }

            return $token;
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    private function generateCardNumber($length)
    {
        $result = '';
        for ($i=0; $i < $length; $i++) { 
            $result .= mt_rand(0, 9);
        }

        $wallet = Wallet::where('card_number', $result)->exists();

        if ($wallet) {
            return $this->generateCardNumber($length);
        }

        return $result;
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
