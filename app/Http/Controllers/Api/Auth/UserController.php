<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    public function index()
    {
        try{
            $datas = User::where('nrk', '!=', 'ADM')
                            ->where('grade', '!=', '0')
                            ->select('id', 'name', 'jabatan', 'divisi', 'departemen', 'grade', 'nrk')
                            ->with(['akses'])
                            ->orderBy('name')->get();
            if ($datas->isEmpty()) {
                return response()->json([
                    'message' => 'Record not found',
                    'code' => 200,
                    'success' => true
                ], 200);
            }
            return response()->json([
                'data' => $datas,
                'message' => 'Success to Fetch All Datas',
                'code' => 200,
                'success' => true
            ], 200);

        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }

    public function show(Request $request)
    {   
        try{
            $authorizationHeader = $request->header('Authorization');

            if (strpos($authorizationHeader, 'Bearer ') === 0) {
                $jwt = str_replace('Bearer ', '', $authorizationHeader);
            } else {
                return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
            }
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));
            
            $data = User::select('id', 'name', 'jabatan', 'divisi', 'departemen', 'grade', 'nrk')->findOrFail($decoded->sub);
            return response()->json([
                'data' => $data,
                'message' => 'Success to Fetch All Datas',
                'code' => 200,
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }

    public function get($id)
    {   
        try{
            $data = User::findOrFail($id);
            return response()->json([
                'data' => $data,
                'message' => 'Success to Fetch All Datas',
                'code' => 200,
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }
}
