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
        $app_id = 16;

            if (strpos($authorizationHeader, 'Bearer ') === 0) {
                $jwt = str_replace('Bearer ', '', $authorizationHeader);
            } else {
                return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
            }

        if ($jwt) {
            try {
                $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

                $akses = MasterAkses::where('app_id', $app_id)
                                        ->where('user_id', $decoded->sub)
                                        ->first();

                if ($decoded && $akses->level_akses >= 10 && Carbon::now()->timestamp < $decoded->exp) {
                    return $next($request);
                }else{
                    return response()->json(['code' => 401,'error' => 'Unauthorized'], 401);
                }
            } catch (\Exception $e) {
                // redirect ke login
                return response()->json(['code' => 401,'error' => 'Invalid or expired token'], 401);
            }
        }else{
            // Redirect to login or return an error response
            return response()->json(['code' => 401,'error' => 'Unauthorized'], 401);
        }

    }
}
