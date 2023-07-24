<?php

namespace App\Http\Controllers\Api\Miscellaneous;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CurrencyController extends Controller
{
    function kurs() {

        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');

        $url = "https://www.bi.go.id/biwebservice/wskursbi.asmx/getSubKursAsing3?mts=USD&startdate={$formattedDate}&enddate={$formattedDate}";
        $xmlString = file_get_contents($url);

        $xml = simplexml_load_string($xmlString);

        $data = [];
        foreach ($xml->children('diffgr', true)->diffgram->children()->NewDataSet->children() as $table) {
            $id = (string) $table->id_subkursasing;
            $link = (string) $table->lnk_subkursasing;
            $nilai = (string) $table->nil_subkursasing;
            $beli = (string) $table->beli_subkursasing;
            $jual = (string) $table->jual_subkursasing;
            $tgl = (string) $table->tgl_subkursasing;
            $mts = (string) $table->mts_subkursasing;

            $data = [
                'id_subkursasing' => $id,
                'lnk_subkursasing' => $link,
                'nil_subkursasing' => $nilai,
                'beli_subkursasing' => $beli,
                'jual_subkursasing' => $jual,
                'tgl_subkursasing' => $tgl,
                'mts_subkursasing' => $mts,
            ];
        }

        return $data;
    }
}
