<?php

namespace App\Http\Controllers\Api\Miscellaneous;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class AlamatController extends Controller
{
    // Provinsi
    public function getProv(){
        $url = "https://dev.farizdotid.com/api/daerahindonesia/provinsi";

        $response = Http::get($url);
        $array = $response->json();

        if (array_key_exists('provinsi', $array) && !is_null($array['provinsi'])) {
            usort($array['provinsi'], function($a, $b){
                return strcmp($a['nama'], $b['nama']);
            });
        }
        return response()->json($array);
    }

    public function detailProv($id_prov){
        $url = "https://dev.farizdotid.com/api/daerahindonesia/provinsi/".$id_prov;

        $response = Http::get($url);
        $array = $response->json();

        return response()->json($array);
    }

    // Kota/Kab
    public function getKabKot($id_prov){     //based on prov id
        $url = "https://dev.farizdotid.com/api/daerahindonesia/kota?id_provinsi=".$id_prov;

        $response = Http::get($url);
        $array = $response->json();
        // dd($array);
        if (array_key_exists('kota_kabupaten', $array) && !is_null($array['kota_kabupaten'])) {
            usort($array['kota_kabupaten'], function($a, $b){
                return strcmp($a['nama'], $b['nama']);
            });
        }

        return response()->json($array);
    }

    public function detailKabKot($id_kabkot){
        $url = "https://dev.farizdotid.com/api/daerahindonesia/kota/".$id_kabkot;

        $response = Http::get($url);
        $array = $response->json();

        return response()->json($array);
    }

    //Kec
    public function getKec($id_kabkot){     //based on kabkot id
        $url = "https://dev.farizdotid.com/api/daerahindonesia/kecamatan?id_kota=".$id_kabkot;

        $response = Http::get($url);
        $array = $response->json();
        // dd($array);
        if (array_key_exists('kecamatan', $array) && !is_null($array['kecamatan'])) {
            usort($array['kecamatan'], function($a, $b){
                return strcmp($a['nama'], $b['nama']);
            });
        }

        return response()->json($array);
    }

    public function detailKec($id_kec){
        $url = "https://dev.farizdotid.com/api/daerahindonesia/kecamatan/".$id_kec;

        $response = Http::get($url);
        $array = $response->json();

        return response()->json($array);
    }

    //Kelurahan
    public function getKel($id_kec){     //based on kabkot id
        $url = "https://dev.farizdotid.com/api/daerahindonesia/kelurahan?id_kecamatan=".$id_kec;

        $response = Http::get($url);
        $array = $response->json();
        // dd($array);
        if (array_key_exists('kelurahan', $array) && !is_null($array['kelurahan'])) {
            usort($array['kelurahan'], function($a, $b){
                return strcmp($a['nama'], $b['nama']);
            });
        }

        return response()->json($array);
    }

    public function detailKel($id_kec){
        $url = "https://dev.farizdotid.com/api/daerahindonesia/kelurahan/".$id_kec;

        $response = Http::get($url);
        $array = $response->json();

        return response()->json($array);
    }
}
