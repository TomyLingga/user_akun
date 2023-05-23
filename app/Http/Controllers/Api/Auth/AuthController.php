<?php

namespace App\Http\Controllers\Api\Auth;
use Firebase\JWT\JWT;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $token = null;

        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Invalid email or password', 'code' => 401], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to create token', 'code' => 500], 500);
        }

        $user = auth()->user();

        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'iat' => time(),
            'exp' => time() + (4 * 60 * 60) // token will expire in 1 hour
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        $user->remember_token = $token;
        $user->save();

        return response()->json(['message' => 'Successfully login','token' => $token, 'code' => 200], 200)
                        ->withCookie(cookie('jwt', $token, time() + (4 * 60 * 60)));
                        // ->withHeaders([
                            // 'Content-Type' => 'application/json;charset=utf-8',
                            // 'Cookie' => $token.'; HttpOnly; Max-Age=',
                            // 'Access-Control-Allow-Origin' => '*'
        // ]);
    }

    public function logout(Request $request)
    {   
        $token = $request->cookie('jwt');

        // Delete the JWT token from the user's record in the database
        $user = User::where('remember_token', $token)->first();
        $user->remember_token = null;
        $user->save();

        // Remove the JWT cookie
        $cookie = cookie('jwt', null, -1);

        return response()->json(['message' => 'Successfully logged out', 'code' => 200], 200)->withCookie($cookie);
    }
    
    public function auth_checker(Request $request)
    {
        $jwt = $request->header('JWT');

        try {
            $user = User::where('remember_token', $jwt)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'code' => 200
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500
            ]);
        }
        
    }

    //

}
