<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class TokenChecker
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');

            if (strpos($authorizationHeader, 'Bearer ') === 0) {
                $jwt = str_replace('Bearer ', '', $authorizationHeader);
            } else {
                return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
            }
        
        if ($jwt) {
            try {
                $user = User::where('remember_token', $jwt)->first();
                $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

                if ($user && Carbon::now()->timestamp < $decoded->exp) {
                    return $next($request);
                }
            } catch (\Exception $e) {
                // redirect ke login
                return response()->json(['error' => 'Invalid or expired token'], 401);
            }
        }

        // Redirect to login or return an error response
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}