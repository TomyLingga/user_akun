<?php

namespace App\Http\Controllers\Api\Miscellaneous;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class KampusController extends Controller
{
    public function getKampus(){
        $url = "https://api-frontend.kemdikbud.go.id/loadpt";

        $response = Http::get($url); // fetch value dari url
        $array = $response->json(); // jadiin json

        $newarray = array();
        $processed = array();

        for ($i=0; $i < count($array) ; $i++) {
            if($array[$i]['nama_pt'] == '' || $array[$i]['nama_pt'] == ' ' || $array[$i]['nama_pt'] == null){
                continue;
            }
            $univ = ltrim($array[$i]['nama_pt']);
            if (!in_array($univ, $processed)) {
                $newarray[] = array(
                    'univ' => $univ,
                    'id_sp' => ltrim($array[$i]['id_sp']),
                    'kode_pt' => ltrim($array[$i]['kode_pt'])
                );
                $processed[] = $univ;
            }
        }

        usort($newarray, function ($a, $b) {
            return strcmp($a["univ"], $b["univ"]);
        });
        return response()->json(['data' => $newarray]);

    }

    public function getProdi($id_sp){
        $url = "https://api-frontend.kemdikbud.go.id/v2/detail_pt_prodi/".$id_sp;

        $response = Http::get($url); // fetch value dari url
        $array = $response->json(); // jadiin json

        $data = collect($array)->map(function ($item) {
            return [
                'nm_lemb' => $item['nm_lemb'],
                'jenjang' => $item['jenjang'],
                'akreditas' => $item['akreditas'],
            ];
        })->sortBy('nm_lemb')->values()->all();

        return response()->json(['data' => $array]);
    }

    //https://api-frontend.kemdikbud.go.id/v2/detail_pt/{{id_sp}}
}
