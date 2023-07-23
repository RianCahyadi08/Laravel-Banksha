<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\TransferHistory;
use App\Models\Wallet;
use App\Models\User;
use App\Models\DataPlan;
use App\Models\DataPlanHistories;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DataPlanController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'data_plan_id' => 'required|integer',
            'phone_number' => 'required|string',
            'pin' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400);
        }

        $userId = auth()->user()->id;
        $transactionType = TransactionType::where('code', 'internet')->first();
        $paymentMethod = PaymentMethod::where('code', 'bwa')->first();
        $userWallet = Wallet::where('user_id', $userId)->first();

        $dataPlan = DataPlan::find($request->data_plan_id);

        if (!$dataPlan) {
            return response()->json([
                'message' => 'Data plan not found',
            ], 404);
        }

        $pinChecker = pinChecker($request->pin);
        if (!$pinChecker) {
            return response()->json([
                'message' => 'Your PIN is wroing'
            ], 400);
        }

        if ($userWallet->balance < $dataPlan->price) {
            return response()->json([
                'message' => 'Your balance is not enough',
            ], 400);
        }

        return response()->json([
            'message' => 'Transaction successfully',
        ]);
    }
}
