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
                    'success' => true
                ], 200);
            }
            return response()->json([
                'data' => $datas,
                'message' => 'Success to Fetch All Datas',
                'success' => true
            ], 200);

        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
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
                    'message' => 'Akses with app_id and user_id already exists.',
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
                'success' => true
            ], 200);

        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'Failed to create data',
                'success' => false
            ], 500);
        }
    }

    public function show($akses_id)
    {
        try{
            $data = MasterAkses::where('akses_id', '=', $akses_id)->first();
            if (is_null($data)) {
                return response()->json('Data not found', 404);
            }
            return response()->json([
                'data' => new MasterAksesResource($data),
                'message' => 'Data MasterAkses found',
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'success' => false
            ], 500);
        }
    }

    public function showApp($app_id)
    {
        try{
            $data = MasterAkses::where('app_id', '=', $app_id)->first();
            if (is_null($data)) {
                return response()->json('Data not found', 404);
            }
            return response()->json([
                'data' => new MasterAksesResource($data),
                'message' => 'Data MasterAkses found',
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'success' => false
            ], 500);
        }
    }

    public function showUser($user_id)
    {
        try{
            $data = MasterAkses::where('user_id', '=', $user_id)->first();
            if (is_null($data)) {
                return response()->json('Data not found', 404);
            }
            return response()->json([
                'data' => new MasterAksesResource($data),
                'message' => 'Data MasterAkses found',
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
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
                    'success' => false
                ], 400);
            }

            $data->update([
                'level_akses' => $request->get('level_akses'),
            ]);

            return response()->json([
                'data' => $data,
                'message' => 'Data Updated Successfully',
                'success' => true
            ],200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'success' => false
            ], 500);
        }
    }
}
