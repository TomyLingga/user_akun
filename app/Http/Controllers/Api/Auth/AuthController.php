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
// use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $token = null;

        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Invalid email or password'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to create token'], 500);
        }

        $user = auth()->user();

        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'iat' => time(),
            'exp' => time() + (60 * 60) // token will expire in 1 hour
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        $user->remember_token = $token;
        $user->save();

        $data = new Response(json_encode(['message' => 'Login success']), 200);
        $data->withCookie(cookie('jwt', $token, time() + (60 * 60)));
        return $data;
        
    }
    // return response()->json([
    //     'success' => true
    // ]);
    // return response()->json([
    //     'access_token' => $token,
    //     'token_type' => 'bearer',
    //     'expires_in' => time() + (60 * 60)
    // ]);

    public function auth_checker(Request $request){
        $jwt = $request->header('JWT');

        try {
            $user = User::where('remember_token', $jwt)->firstOrFail();
            
            return response()->json([
                'success' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false
            ]);
        }
        
    }

    public function logout(Request $request)
    {
        // Delete the JWT token from the user's record in the database
        $user = Auth::guard('api')->user();
        $user->remember_token = null;
        $user->save();

        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    //

}
