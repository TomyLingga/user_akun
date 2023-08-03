<?php

namespace App\Http\Controllers\Api\Position;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class DivisionController extends Controller
{
    public function bom()
    {
        try{
            $datas = User::where(function ($query) {
                            $query->where('grade', '13')
                                ->orWhere('grade', '6');
                        })
                        ->get();

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

    public function index()
    {
        try{
            $datas = Division::orderBy('divisi')
                                ->with('departments')
                                ->get();

            if ($datas->isEmpty()) {
                return response()->json([
                    'message' => 'Record not Found',
                    'code' => 404,
                    'success' => true
                ], 404);
            }

            return response()->json([
                'data' => $datas,
                'message' => 'Success to Fetch All Datas',
                'code' => 200,
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Fetch All Datas', 'code' => 500], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'divisi' => 'required',
                'bom' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ], 400);
            }

            $data = Division::create([
                'divisi' => $request->get('divisi'),
                'bom' => $request->get('bom'),
                'status' => '1'
            ]);

            return response()->json([
                'data' => $data,
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

    public function show($id)
    {
        try{
            $data = Division::where('id', $id)->first();

            if (is_null($data)) {
                return response()->json(['message' => 'Data not found', 'code' => 404], 404);
            }
            return response()->json([
                'data' => $data,
                'message' => 'Data found',
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

    public function update(Request $request, $id)
    {
        try {
            $data = Division::where('id', $id)->first();
            if (is_null($data)) {
                return response()->json('Data not found', 404);
            }

            $divisi = $request->filled('divisi') ? $request->get('divisi') : $data->divisi;
            $bom = $request->filled('bom') ? $request->get('bom') : $data->bom;

            $data->update([
                'divisi' => $divisi,
                'bom' => $bom,
            ]);

            return response()->json([
                'data' => $data,
                'message' => 'Data Updated Successfully',
                'code' => 200,
                'success' => true
            ], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }

    public function toggleActive($id)
    {
        try {

            $data = Division::findOrFail($id);

            $newStatusValue = ($data->status == 0) ? 1 : 0;

            $data->update([
                'status' => $newStatusValue,
            ]);

            $message = ($newStatusValue == 0) ? 'Status updated to Non-Active.' : 'Status updated to Active.';

            return response()->json([
                'data' => $data,
                'message' => $message,
                'code' => 200,
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => "Something went wrong",
                'err' => $e->getTrace()[0],
                'errMsg' => $e->getMessage(),
                'code' => 500,
                'success' => false
            ], 500);
        }
    }
}
