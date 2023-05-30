<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\MasterApps;
use App\Models\User;
use Carbon\Carbon;


class PortalController extends Controller
{   
    // private $jwtSecret = "your_jwt_secret_here";
    private $image_path = 'http://36.92.181.10:4763/storage/upload/icon/';
    
    public function index()
    {
        // daftar app
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Request $request)
    {   
        try{
            // $data = MasterApps::where('app_id', $request->app_id)->first();
            // if (is_null($data)) {
            //     return response()->json('Record not Found', 404);
            // }
            // $url = $data->url_app;
            $authorizationHeader = $request->header('Authorization');

            if (strpos($authorizationHeader, 'Bearer ') === 0) {
                $token = str_replace('Bearer ', '', $authorizationHeader);
            } else {
                return response()->json(['error' => 'Invalid Authorization header', 'code' => 401], 401);
            }

            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            dd($decoded->divisi);


            $user = User::where('id', $decoded->sub)
                            ->select('id','name','jabatan','divisi','departemen','grade')
                            ->first();
                            
            $data = new Response(['url' => $url, 'user' => $user, 'code' => 200], 200);
            // $data->withCookie(cookie('jwt', $token, time() + (60 * 60)));
            return $data;
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Failed to Get Data', 'code' => 500], 500);
        }
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
