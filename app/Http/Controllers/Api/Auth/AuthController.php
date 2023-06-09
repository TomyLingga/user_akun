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
            'jabatan' => $user->jabatan,
            'departemen' => $user->departemen,
            'divisi' => $user->divisi,
            'nrk' => $user->nrk,
            'grade' => $user->grade,
            'iat' => time(),
            'exp' => time() + (4 * 60 * 60) // token will expire in 1 hour
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        $user->remember_token = $token;
        $user->save();

        return response()->json(['message' => 'Successfully login','token' => $token, 'code' => 200, 'payload' => $payload], 200)
                        // ->withCookie(cookie('jwt', $token, time() + (4 * 60 * 60)));
                        ->withHeaders([
                            'Content-Type' => 'application/json;charset=utf-8',
                            'Cookie' => $token.'; HttpOnly; Max-Age=',
                            'Access-Control-Allow-Origin' => '*'
        ]);
    }

    public function logout(Request $request)
    {   
        // $token = $request->cookie('jwt');
        $authorizationHeader = $request->header('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $authorizationHeader);
        } else {
            return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
        }

        $user = User::where('remember_token', $token)->first();
        $user->remember_token = null;
        $user->save();

        //set the Authorization value to null

        return response()->json(['message' => 'Successfully logged out', 'code' => 200], 200);
    }
    
    public function auth_checker(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $authorizationHeader);
        } else {
            return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
        }

        try {
            $user = User::where('remember_token', $token)->firstOrFail();
            
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
