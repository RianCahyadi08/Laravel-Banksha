<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class RedirectPaymentController extends Controller
{

    public function finished(Request $request)
    {   
        $transactionCode = $request->order_id;
        $transaction = Transaction::where('transaction_code', $transactionCode)->first();

        return view('payment-finished', ['transaction' => $transaction]);
    }
}
