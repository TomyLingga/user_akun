<?php

namespace App\Http\Controllers\Api\Apps;

use App\Http\Controllers\Controller;
use App\Models\MasterAkses;
use App\Models\MasterApps;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;



class AppsController extends Controller
{

    private $image_path = 'http://36.92.181.10:4763/storage/upload/icon/';

    public function index()
    {
        try{
            // $datas = MasterApps::where('status_app', '1')->latest()->get();
            $datas = MasterApps::where('status_app', '1')
                                ->orderBy('nama_app')
                                ->get();

            if ($datas->isEmpty()) {
                return response()->json([
                    'message' => 'Record not Found',
                    'code' => 404,
                    'success' => true
                ], 404);
            }

            foreach ( $datas as $key => $file ) {
                $file->logo_app = $this->image_path.$file->logo_app;
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

    public function index_all()
    {
        try{
            $datas = MasterApps::orderBy('nama_app')
                                ->get();

            if ($datas->isEmpty()) {
                return response()->json([
                    'message' => 'Record not Found',
                    'code' => 404,
                    'success' => true
                ], 404);
            }

            foreach ( $datas as $key => $file ) {
                $file->logo_app = $this->image_path.$file->logo_app;
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
                    'code' => 400,
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

                $users = User::all();

                foreach ($users as $user) {
                    $aksesData = [
                        'user_id' => $user->id,
                        'app_id' => $apps->app_id,
                        'level_akses' => '1',
                    ];

                    MasterAkses::create($aksesData);
                }
            }else{
                return response()->json(['message' => 'Failed to Create Data.', 'code' => 500], 500);
            }

            return response()->json([
                'data' => $apps,
                'message' => 'Data Created Successfully.',
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Create Data.', 'code' => 500], 500);
        }
    }

    // by low id
    public function show($app_id)
    {
        try{
            $data = MasterApps::where('app_id', $app_id)->first();
            if (is_null($data)) {
                return response()->json(['message' => 'Record not Found','code' => 404], 404);
            }
            $data->logo_app = $this->image_path.$data->logo_app;
            return response()->json([
                'data' => $data,
                'message' => 'Data Found',
                'code' => 200,
                'success' => true
            ], 200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Get Data', 'code' => 500], 500);
        }
    }

    // by low id
    public function update(Request $request, $app_id)
    {
        try{
            if (!MasterApps::where('app_id', $app_id)->exists()) {
                return response()->json(['message' => 'Data not found', 'code' => 404], 404);
            }
            $data = MasterApps::where('app_id', $app_id)->first();

            $validator = Validator::make($request->all(), [
                'nama_app' => 'required',
                'url_app' => 'required',
                'logo_app' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'data' => [],
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ]);
            }
            $input = $request->all();
            $originalName = $input['logo_app']->getClientOriginalName();
            $newName = time().'_'.str_replace(' ', '_', $originalName);

            if($request->logo_app->move('storage/upload/icon/', $newName ) ) {
                $data->update([
                    'nama_app' => $request->get('nama_app'),
                    'url_app' => $request->get('url_app'),
                    'logo_app' => $newName,
                    'status_app' => '0'
                ]);
            }else{
                return response()->json(['message' => 'Failed to Update Data.', 'code' => 500], 500);
            }

            return response()->json([
                'data' => $data,
                'message' => 'Data Updated Successfully',
                'code' => 200,
                'success' => true
            ],200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Update Data', 'code' => 500], 500);
        }
    }

    public function togglePost($app_id)
    {
        $data = MasterApps::find($app_id);

        if (!$data) {
            return response()->json([
                'message' => 'Record not found.',
                'code' => 404,
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
            'code' => 200,
            'success' => true
        ],200);
    }
}
