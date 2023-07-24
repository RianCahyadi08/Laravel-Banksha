<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit') ? $request->query('limit') : 5;

        $user = auth()->user();

        $relations = [
            'paymentMethod:id,name,code,thumbnail',
            'transactionType:id,name,code,action,thumbnail',
        ];

        $transactions = Transaction::with($relations)
                        ->where('user_id', $user->id)
                        ->where('status', 'success')
                        ->orderBy('id', 'desc')
                        ->paginate($limit);

        $transactions->getCollection()->transform(function ($item) {
            $paymentMethod = $item->paymentMethod;
            $item->paymentMethod->thumbnail = $paymentMethod->thumbnail ? url('banks/'.$paymentMethod->thumbnail) : "";
        
            $transactionType = $item->transactionType;
            $item->transactionType->thumbnail = $transactionType->thumbnail ? url('transaction-type/'.$transactionType->thumbnail) : "";
        
            return $item;
        });

        return response()->json($transactions);
    }
}
