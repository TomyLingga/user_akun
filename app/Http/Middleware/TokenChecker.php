<?php

namespace App\Http\Middleware;

use App\Models\Token;
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
                // Decode the JWT token
                $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

                // Check if the token exists in the Token model
                $tokenEntry = Token::where('token', $jwt)->first();

                if ($tokenEntry && Carbon::now()->timestamp < $decoded->exp) {
                    // Add token and decoded payload to the request
                    $request->merge(['user_token' => $authorizationHeader, 'decoded' => $decoded]);

                    return $next($request);
                } else {
                    return response()->json(['error' => 'You do not have access for this', 'code' => 401], 401);
                }
            } catch (\Exception $e) {
                // Handle token decoding errors
                return response()->json(['code' => 401, 'error' => 'Invalid or expired token'], 401);
            }
        }

        // Return unauthorized if no JWT is provided
        return response()->json(['code' => 401, 'error' => 'Unauthorized'], 401);
    }

}
