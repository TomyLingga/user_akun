<?php

namespace App\Http\Controllers\Api\Apps;

use App\Http\Controllers\Controller;
use App\Models\MasterApps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;



class AppsController extends Controller
{
    
    private $image_path = 'http://192.168.0.173:8888/storage/upload/icon/';

    public function index()
    {
        try{
            // $datas = MasterApps::where('status_app', '1')->latest()->get();
            $datas = MasterApps::latest()->get();

            if ($datas->isEmpty()) {
                return response()->json([
                    'message' => 'Record not Found',
                    'success' => true
                ], 404);
            }

            foreach ( $datas as $key => $file ) {
                $file->logo_app = $this->image_path.$file->logo_app;
            }

            return response()->json([
                'data' => $datas,
                'message' => 'Success to Fetch All Datas',
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Fetch All Datas'], 500);
        }
    }

    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'nama_app' => 'required',
                'url_app' => 'required',
                'logo_app' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'data' => [],
                    'message' => $validator->errors(),
                    'success' => false
                ]);
            }

            $input = $request->all();
            $originalName = $input['logo_app']->getClientOriginalName();
            $newName = time().'_'.str_replace(' ', '_', $originalName);

            if($request->logo_app->move('storage/upload/icon/', $newName ) ) {
                $apps = MasterApps::create([
                    'nama_app' => $request->get('nama_app'),
                    'url_app' => $request->get('url_app'),
                    'logo_app' => $newName,
                    'status_app' => '0'
                ]);
            }else{
                return response()->json(['message' => 'Failed to Create Data.'], 500);
            }

            return response()->json([
                'data' => $apps,
                'message' => 'Data Created Successfully.',
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Create Data.'], 500);
        }
    }

    // by low id
    public function show($app_id)
    {
        try{
            $data = MasterApps::where('app_id', $app_id)->first();
            if (is_null($data)) {
                return response()->json('Record not Found', 404);
            }
            $data->logo_app = $this->image_path.$data->logo_app;
            return response()->json([
                'data' => $data,
                'message' => 'Data Found',
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Get Data'], 500);
        }
    }

    // by low id
    public function update(Request $request, $app_id)
    {
        try{
            if (!DataApps::where('app_id', $app_id)->exists()) {
                return response()->json('Data not found', 404);
            }
            $data = DataApps::where('app_id', $app_id)->first();

            $validator = Validator::make($request->all(), [
                'nama_app' => 'required',
                'url_app' => 'required',
                'logo_app' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'data' => [],
                    'message' => $validator->errors(),
                    'success' => false
                ]);
            }
            $input = $request->all();
            $originalName = $input['logo_app']->getClientOriginalName();
            $newName = time().'_'.str_replace(' ', '_', $originalName);

            if($request->doc_low->move('storage/upload/icon/', $newName ) ) {
                $data->update([
                    'nama_app' => $request->get('nama_app'),
                    'url_app' => $request->get('url_app'),
                    'logo_app' => $newName,
                    'status_app' => '0'
                ]);
            }else{
                return response()->json(['message' => 'Failed to Update Data.'], 500);
            }

            return response()->json([
                'data' => $data,
                'message' => 'Data Updated Successfully',
                'success' => true
            ],200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Update Data'], 500);
        }
    }

    public function togglePost($app_id)
    {
        $data = DataApps::find($app_id);

        if (!$data) {
            return response()->json([
                'message' => 'Record not found.',
                'success' => false
            ], 404);
        }

        $newStatusValue = $data->status_app == 1 ? 0 : 1;

        $data->update([
            'status_app' => $newStatusValue,
        ]);

        $message = $newStatusValue == 1 ? 'App Posted Successfully' : 'App Unposted Successfully';

        return response()->json([
            'data' => [],
            'message' => $message,
            'success' => true
        ],200);
    }
}
