<?php

namespace App\Http\Controllers\Api\Akses;

use App\Http\Controllers\Controller;
use App\Models\MasterAkses;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AksesController extends Controller
{
    public function index()
    {
        try{
            $datas = MasterAkses::latest()->with(['user', 'apps'])->get();
            if ($datas->isEmpty()) {
                return response()->json([
                    'message' => 'Record not found',
                    'success' => true,
                    'code' => 200
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

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'app_id' => 'required',
                'user_id' => 'required',
                'level_akses' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ], 400);
            }

            $app_id = $request->get('app_id');
            $user_id = $request->get('user_id');

            $existingAkses = MasterAkses::where('app_id', $app_id)
                                        ->where('user_id', $user_id)
                                        ->first();

            if ($existingAkses) {
                return response()->json([
                    'message' => 'Akses for this app and user already exists.',
                    'code' => 409,
                    'success' => false
                ], 409);
            }

            $MasterAkses = MasterAkses::create([
                'app_id' => $app_id,
                'user_id' => $user_id,
                'level_akses' => $request->get('level_akses')
            ]);

            return response()->json([
                'data' => $MasterAkses,
                'message' => 'Data Created Successfully.',
                'code' => 200,
                'success' => true
            ], 200);

        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'Failed to create data',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }

    public function show($akses_id)
    {
        try{
            $data = MasterAkses::where('akses_id', '=', $akses_id)->first();
            if (is_null($data)) {
                return response()->json(['message' => 'Data not found', 'code' => 404], 404);
            }
            return response()->json([
                'data' => $data,
                'message' => 'Data MasterAkses found',
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

    public function showApp($app_id)
    {
        try{
            $data = MasterAkses::where('app_id', '=', $app_id)->get();
            if (is_null($data)) {
                return response()->json(['message' => 'Data not found', 'code' => 404], 404);
            }
            return response()->json([
                'data' => $data,
                'message' => 'Data MasterAkses found',
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

    public function showUser($user_id)
    {
        try{
            $data = MasterAkses::where('user_id', '=', $user_id)->get();
            if (is_null($data)) {
                return response()->json(['message' => 'Data not found', 'code' => 404], 404);
            }
            return response()->json([
                'data' => $data,
                'message' => 'Data MasterAkses found',
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

    public function showMine($app_id, $user_id)
    {
        try{
            $data = MasterAkses::where('user_id', $user_id)->where('app_id', $app_id)->first();
            if (is_null($data)) {
                return response()->json(['message' => 'Data not found', 'code' => 404], 404);
            }
            return response()->json([
                'data' => $data,
                'message' => 'Data MasterAkses found',
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

    public function update(Request $request, $akses_id)
    {
        try{
            $data = MasterAkses::where('akses_id', '=', $akses_id)->first();
            if (is_null($data)) {
                return response()->json('Data not found', 404);
            }
            $validator = Validator::make($request->all(), [
                'level_akses' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ], 400);
            }

            $data->update([
                'level_akses' => $request->get('level_akses'),
            ]);

            return response()->json([
                'data' => $data,
                'message' => 'Data Updated Successfully',
                'code' => 200,
                'success' => true
            ],200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }
}
