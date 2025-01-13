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

class AdminItChecker
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');
        $app_id = 16; // Application ID to validate access levels

        // Check and extract JWT from the Authorization header
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $jwt = str_replace('Bearer ', '', $authorizationHeader);
        } else {
            return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
        }

        if ($jwt) {
            try {
                // Decode the JWT token
                $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

                // Fetch user access level from MasterAkses
                $akses = MasterAkses::where('app_id', $app_id)
                    ->where('user_id', $decoded->sub)
                    ->first();

                // Validate the token, access level, and expiration time
                if ($decoded && $akses && $akses->level_akses >= 10 && Carbon::now()->timestamp < $decoded->exp) {
                    $request->merge(['user_token' => $jwt, 'decoded' => $decoded]);
                    return $next($request);
                } else {
                    return response()->json(['code' => 401, 'error' => 'Unauthorized access'], 401);
                }
            } catch (\Exception $e) {
                // Handle invalid or expired token
                return response()->json(['code' => 401, 'error' => 'Invalid or expired token'], 401);
            }
        }

        // Fallback for missing JWT
        return response()->json(['code' => 401, 'error' => 'Unauthorized'], 401);
    }

}
