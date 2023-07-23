<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WebHookController extends Controller
{

    public function update()
    {
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $type = $notif->payment_type;
        $transactionCode = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        DB::beginTransaction();
        try {
            $status = null;
            if ($transactionStatus == 'capture'){
                if ($fraudStatus == 'accept'){
                    $status = 'success';
                } else if ($fraudStatus == 'challenge') {
                    $status = 'challenge';
                }
            } else if ($transactionStatus == 'settlement'){
                $status = 'success';
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire'){
                $status = 'failed';
            } else if ($transactionStatus == 'pending'){
                $status = 'pending';
            }

            $transaction = Transaction::where('transaction_code', $transactionCode)->first();

            if ($transaction->status != 'success') {
                $transactionAmount = $transaction->amount;
                $userId = $transaction->user_id;

                $transaction->update([
                    'status' => $status,
                ]);

                if ($status == 'success') {
                    Wallet::where('user_id', $userId)->increment('balance', $transactionAmount);
                }
            }

            DB::commit();
            return response()->json();
        } catch (\Throwable $th) {
            DB::rollback();
            echo $th;
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }
}
