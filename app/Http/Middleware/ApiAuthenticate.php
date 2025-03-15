<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Try to parse the token from the request and authenticate the user
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            // Handle expired or invalid tokens and return 401 Unauthorized
            return response()->json(['error' => 'Unauthorized, Token expired or invalid'], 401);
        }

        return $next($request);
    }
}

