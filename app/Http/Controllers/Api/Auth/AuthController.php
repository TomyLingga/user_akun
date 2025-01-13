<?php

namespace App\Http\Controllers\Api\Auth;
use Firebase\JWT\JWT;
use App\Http\Controllers\Controller;
use App\Models\Token;
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
            'no_hp' => $user->noHP,
            'foto' => $user->foto,
            'signature' => $user->signature,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60)
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        Token::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

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
        // Extract the token from the Authorization header
        $authorizationHeader = $request->header('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $authorizationHeader);
        } else {
            return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
        }

        // Find and delete the token from the Token model
        $deleted = Token::where('token', $token)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Successfully logged out', 'code' => 200], 200);
        } else {
            return response()->json(['error' => 'Token not found', 'code' => 404], 404);
        }
    }

    public function auth_checker(Request $request)
    {
        // Extract the token from the Authorization header
        $authorizationHeader = $request->header('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $authorizationHeader);
        } else {
            return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
        }

        try {
            // Check if the token exists in the Token model
            $tokenEntry = \App\Models\Token::where('token', $token)->firstOrFail();

            return response()->json([
                'success' => true,
                'code' => 200,
                'user_id' => $tokenEntry->user_id, // Optionally return the user ID
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 401, // Use 401 for unauthorized
                'error' => 'Token not found or invalid',
            ]);
        }
    }


    //

}
