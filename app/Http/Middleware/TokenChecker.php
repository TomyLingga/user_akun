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
        $jwt = $request->cookie('jwt');
        
        if ($jwt) {
            try {
                $user = User::where('remember_token', $jwt)->first();
                // dd(JWT::decode($jwt, env('JWT_SECRET'), ['HS256']));
                // $decoded = JWT::decode($jwt, env('JWT_SECRET'), ['HS256']);
                $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

                // dd($decoded->exp, Carbon::now()->timestamp);
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
