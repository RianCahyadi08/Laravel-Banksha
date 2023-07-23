<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransferHistory;

class TransferHistoriesController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit') ? $request->query('limit') : 5;

        $sender = auth()->user();

        $transferHistories = TransferHistory::with('receiverUser:id,name,username,verified,profile_picture')
                            ->select('receiver_id')
                            ->where('sender_id', $sender->id)
                            ->groupBy('receiver_id')
                            ->paginate($limit);

        // echo $transferHistories;
        // echo $sender;
        $transferHistories->getCollection()->transform(function ($item) {
            $receiverUser = $item->receiverUser;
            $receiverUser->profile_picture = $receiverUser->profile_picture ? url('storage/'.$receiverUser->profile_picture) : "";

            return $receiverUser;
        });

        return response()->json($transferHistories);
    }
}
