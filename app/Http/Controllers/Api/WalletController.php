<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $wallet = Wallet::select('pin', 'balance', 'card_number')
                    ->where('user_id', $user->id)
                    ->first();

        return response()->json($wallet);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'previous_pin' => 'required|digits:6',
            'new_pin' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator,
            ]);
        }

        if (!pinChecker($request->previous_pin)) {
            return response()->json([
                'message' => 'Your old pin is wrong',
            ], 400);
        }

        $user = auth()->user();
        $newPin = $request->new_pin;

        Wallet::where('user_id', $user->id)
                ->update(['pin' => $newPin]);

        return response()->json([
            'message' => 'Successfully updated pin',
        ]);
    }
}
