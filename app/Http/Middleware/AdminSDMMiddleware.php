<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\MasterAkses;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class AdminSDMMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $app_id = 16; // Application ID to check access
        $authorizationHeader = $request->header('Authorization');

        // Extract the JWT from the Authorization header
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $jwt = str_replace('Bearer ', '', $authorizationHeader);
        } else {
            return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
        }

        if ($jwt) {
            try {
                // Decode the JWT using the secret key
                $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

                // Validate user access in the MasterAkses table
                $akses = MasterAkses::where('app_id', $app_id)
                    ->where('user_id', $decoded->sub)
                    ->first();

                // Check if token is valid, access level meets requirements, and token is not expired
                if ($akses && $akses->level_akses >= 8 && Carbon::now()->timestamp < $decoded->exp) {
                    // Add decoded token and authorization header to the request
                    $request->merge([
                        'user_token' => $jwt,
                        'decoded' => $decoded
                    ]);

                    return $next($request); // Proceed to the next middleware or controller
                } else {
                    return response()->json(['error' => 'You do not have access for this', 'code' => 401], 401);
                }
            } catch (\Exception $e) {
                // Catch decoding or validation errors
                return response()->json(['error' => 'Invalid or expired token', 'code' => 401], 401);
            }
        }

        // Handle missing JWT
        return response()->json(['error' => 'Unauthorized', 'code' => 401], 401);
    }

}
