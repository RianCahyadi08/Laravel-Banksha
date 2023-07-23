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
        
        $transactionCode = strtoupper(Str::random(10));
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

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $userId,
                'transaction_type_id' => $transactionType->id,
                'payment_method_id' => $paymentMethod->id,
                'transaction_code' => $transactionCode,
                // 'product_id' => $dataPlan->id,
                'description' => 'Internet data plan '.$dataPlan->name,
                'amount' => $dataPlan->price,
                'status' => 'success'
            ]);

            $dataPlanHistories = DataPlanHistories::create([
                'data_plan_id' => $request->data_plan_id,
                'transaction_id' => $transaction->id,
                'phone_number' => $request->phone_number,
            ]);

            $userWallet->decrement('balance', $dataPlan->price);
            
            DB::commit();
            return response()->json([
                'message' => 'Buy data plan successfully',
                'transactions' => $transaction,
            ]);

        } catch (\Throwable $th) {
            DB::rollback();
            echo $th;
            // return response()->json([
            //     'errors' => $th->getMessage(),
            // ]);
        }
    }
}
