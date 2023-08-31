<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $token;
    public $userData;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->token = $request->get('user_token');
            $this->userData = $request->get('decoded');
            return $next($request);
        });
    }
}
