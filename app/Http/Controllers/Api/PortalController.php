<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Carbon\Carbon;

class PortalController extends Controller
{   
    // private $jwtSecret = "your_jwt_secret_here";
    
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
        // ambil dari param request->query['app_id'], trus query ke tabel list app, ambil url, tampung ke $url
        $url = 'https://chat.openai.com/';
        // $request->url
        // ambil token user login
        $token = $request->cookie('jwt');

        $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

        $user = User::where('id', $decoded->sub)
                        ->select('id','name','jabatan','divisi','departemen','grade')
                        ->first();
                        
        $data = new Response(['url' => $url, 'user' => $user], 200);
        // $data->withCookie(cookie('jwt', $token, time() + (60 * 60)));
        return $data;
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
