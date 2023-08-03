<?php

namespace App\Http\Controllers\Api\Position;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function index()
    {
        try{
            $datas = Department::with('division')
                            ->orderBy('divisi_id', 'asc')
                            ->orderBy('department', 'asc')
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
                'divisi_id' => 'required',
                'department' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ], 400);
            }

            $data = Department::create([
                'divisi_id' => $request->get('divisi_id'),
                'department' => $request->get('department'),
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
            $data = Department::where('id', $id)->first();

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

    public function showByDivisi($id)
    {
        try{
            $datas = Department::where('divisi_id', $id)->get();

            if (is_null($datas)) {
                return response()->json(['message' => 'Data not found', 'code' => 404], 404);
            }
            return response()->json([
                'data' => $datas,
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
            $data = Department::where('id', $id)->first();
            if (is_null($data)) {
                return response()->json('Data not found', 404);
            }

            $divisi_id = $request->filled('divisi_id') ? $request->get('divisi_id') : $data->divisi_id;
            $department = $request->filled('department') ? $request->get('department') : $data->department;

            $data->update([
                'divisi_id' => $divisi_id,
                'department' => $department,
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

            $data = Department::findOrFail($id);

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
