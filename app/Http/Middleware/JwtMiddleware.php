<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $th) {
            if ($th instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'message' => 'Token invalid'
                ], 401);
            } else if ($th instanceof \Tymon\JWTAuth\Exceptions\TokenExpired) {
                return response()->json([
                    'message' => 'Token expired'
                ], 401);
            } else {
                return response()->json([
                    'message' => 'Authorization not found'
                ], 401);
            }
            // return response()->json([

            // ]);
        }

        return $next($request);
    }
}
