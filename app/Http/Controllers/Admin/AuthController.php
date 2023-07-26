<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\AdminUser;
// use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credential = $request->only('email', 'password');

        // if (Auth::attempt($credential)) {
        //     $request->session()->regenerate();
 
        //     return redirect()->route('admin.dashboard');
        // }

        if (Auth::guard('web')->attempt($credential)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->with('error', 'Invalid Credential');
    }
}
