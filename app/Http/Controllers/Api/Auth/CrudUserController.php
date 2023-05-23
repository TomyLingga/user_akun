<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Mail;



class CrudUserController extends Controller
{   
    private $profil_path = 'http://36.92.181.10:4763/storage/upload/profil/';
    private $ttd_path = 'http://36.92.181.10:4763/storage/upload/ttd/';
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

    public function user_store(Request $request)
    {   
        try {
            
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'nik' => 'required',
                'tgl_lahir' => 'required',
                'prov_lahir' => 'required',
                'kabkot_lahir' => 'required',
                'kelamin' => 'required',
                'noHP' => 'required',
                'pendidikan' => 'required',
                'jurusan' => 'required',
                'agama' => 'required',
                'npwp' => 'required',
                'domisili' => 'required',
                'nrk' => 'required',
                'prov_ktp' => 'required',
                'kabkot_ktp' => 'required',
                'kec_ktp' => 'required',
                'kel_ktp' => 'required',
                'alamat_ktp' => 'required',
                'prov_domisili' => 'required',
                'kabkot_domisili' => 'required',
                'kec_domisili' => 'required',
                'kel_domisili' => 'required',
                'alamat_domisili' => 'required',
                'jabatan' => 'required',
                'divisi' => 'required',
                'departemen' => 'required',
                'job_level' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ], 400);
            }
    
            $existingEmail = User::where('email', $request->email)
                                        ->first();
    
            if ($existingEmail) {
                return response()->json([
                    'message' => 'Email already used.',
                    'code' => 409,
                    'success' => false
                ], 409);
            }

            //tempat lahir
            $tempat_lahir = $this->detailKabKot($request->kabkot_lahir);
            $jsonProvLahir = $tempat_lahir->getContent();
            $data = json_decode($jsonProvLahir, true);
            $kabkot_lahir = $data['nama'];
            
            //prov ktp
            $prov_ktp = $this->detailProv($request->prov_ktp);
            $jsonProvKtp = $prov_ktp->getContent();
            $data = json_decode($jsonProvKtp, true);
            $provKtp = $data['nama'];

                // kabkot ktp
                $kabkot_ktp = $this->detailKabKot($request->kabkot_ktp);
                $jsonKabkotKtp = $kabkot_ktp->getContent();
                $data = json_decode($jsonKabkotKtp, true);
                $kabkotKtp = $data['nama'];

                // kec ktp
                $kec_ktp = $this->detailKec($request->kec_ktp);
                $jsonKecKtp = $kec_ktp->getContent();
                $data = json_decode($jsonKecKtp, true);
                $kecKtp = $data['nama'];

                // kel ktp
                $kel_ktp = $this->detailKel($request->kel_ktp);
                $jsonKelKtp = $kel_ktp->getContent();
                $data = json_decode($jsonKelKtp, true);
                $kelKtp = $data['nama'];

            $final_ktp = $request->alamat_ktp.", Kelurahan ".$kelKtp.", Kecamatan ".$kecKtp.", ".$kabkotKtp.", ".$provKtp;

            //prov domisili
            $prov_domisili = $this->detailProv($request->prov_domisili);
            $jsonProvDomisili = $prov_domisili->getContent();
            $data = json_decode($jsonProvDomisili, true);
            $provDomisili = $data['nama'];

                // kabkot domisili
                $kabkot_domisili = $this->detailKabKot($request->kabkot_domisili);
                $jsonKabkotDomisili = $kabkot_domisili->getContent();
                $data = json_decode($jsonKabkotDomisili, true);
                $kabkotDomisili = $data['nama'];

                // kec domisili
                $kec_domisili = $this->detailKec($request->kec_domisili);
                $jsonKecDomisili = $kec_domisili->getContent();
                $data = json_decode($jsonKecDomisili, true);
                $kecDomisili = $data['nama'];

                // kel domisili
                $kel_domisili = $this->detailKel($request->kel_domisili);
                $jsonKelDomisili = $kel_domisili->getContent();
                $data = json_decode($jsonKelDomisili, true);
                $kelDomisili = $data['nama'];   
            
            $final_domisili = $request->alamat_domisili.", Kelurahan ".$kelDomisili.", Kecamatan ".$kecDomisili.", ".$kabkotDomisili.", ".$provDomisili;
            
            $MasterUser = User::create([
                'name'                  => $request->name,
                'email'                 => $request->email,
                'nik'                   => $request->nik,
                'tgl_lahir'             => $request->tgl_lahir,
                'tempat_lahir'          => $kabkot_lahir,
                'kelamin'               => $request->kelamin,
                'status_perkawinan'     => $request->statuskawin,
                'jlh_tk'                => $request->tanggungan,
                'noHP'                  => $request->noHP,
                'pendidikan'            => $request->pendidikan,
                'jurusan'               => $request->jurusan,
                'agama'                 => $request->agama,
                'npwp'                  => $request->npwp,
                'domisili'              => $request->domisili,
                'alamat_ktp'            => $final_ktp,
                'alamat_domisili'       => $final_domisili,
                'training'              => $request->training,
                'nrk'                   => $request->nrk,
                'kantor'                => $request->office,
                'jabatan'               => $request->jabatan,
                'divisi'                => $request->divisi,
                'departemen'            => $request->departemen,
                'status_karyawan'       => $request->status,
                'grade'                 => $request->job_level,
                'bpjs_kesehatan'        => $request->bpjs_kesehatan,
                'bpjs_ketenagakerjaan'  => $request->bpjs_ketenagakerjaan,
                'tgl_masuk'             => $request->tgl_masuk,
                'tgl_keluar'            => $request->tgl_sampai,
                'masa_kerja'            => $request->masa_kerja,
                'no_rekening'           => $request->norek,
                'rekening'              => $request->rek,
                'faskes_1'              => $request->faskes1,
                'gaji_pokok'            => $request->gaji,
                'tunjangan_tetap'       => $request->tunjangan_t,
                'tunjangan_tidak_tetap' => $request->tunjangan_tt,
                'keterangan'            => $request->keterangan,
                'foto'                  => 'profile.png',
                'password'              => Hash::make('rahasia123'),
                'roles'                 => 'user',
                'bio'                   => 'bio',
                'signature'             => 'default.png',
                'klinik_id'             => '2',
                'created_at'            => Carbon::now()
            ]);
    
            return response()->json([
                'data' => $MasterUser,
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

    public function user_update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $existingEmail = User::where('email', $request->email)
                ->where('id', '!=', $id)
                ->first();

            if ($existingEmail) {
                return response()->json([
                    'message' => 'Email already used.',
                    'code' => 409,
                    'success' => false
                ], 409);
            }

            //tempat lahir
            if ($request->kabkot_lahir !== null) {
                // Get details of kabupaten/kota
                $tempat_lahir = $this->detailKabKot($request->kabkot_lahir);
                $jsonProvLahir = $tempat_lahir->getContent();
                $data = json_decode($jsonProvLahir, true);
                $kabkot_lahir = $data['nama'];
            } else {
                $kabkot_lahir = $user->tempat_lahir;
            }
            
            if ($request->prov_ktp !== null) {
                //prov ktp
                $prov_ktp = $this->detailProv($request->prov_ktp);
                $jsonProvKtp = $prov_ktp->getContent();
                $data = json_decode($jsonProvKtp, true);
                $provKtp = $data['nama'];

                    // kabkot ktp
                    $kabkot_ktp = $this->detailKabKot($request->kabkot_ktp);
                    $jsonKabkotKtp = $kabkot_ktp->getContent();
                    $data = json_decode($jsonKabkotKtp, true);
                    $kabkotKtp = $data['nama'];

                    // kec ktp
                    $kec_ktp = $this->detailKec($request->kec_ktp);
                    $jsonKecKtp = $kec_ktp->getContent();
                    $data = json_decode($jsonKecKtp, true);
                    $kecKtp = $data['nama'];

                    // kel ktp
                    $kel_ktp = $this->detailKel($request->kel_ktp);
                    $jsonKelKtp = $kel_ktp->getContent();
                    $data = json_decode($jsonKelKtp, true);
                    $kelKtp = $data['nama'];

                $final_ktp = $request->alamat_ktp.", Kelurahan ".$kelKtp.", Kecamatan ".$kecKtp.", ".$kabkotKtp.", ".$provKtp;
            } else {
                $final_ktp = $user->alamat_ktp;
            }


            if ($request->prov_domisili !== null) {
                //prov domisili
                $prov_domisili = $this->detailProv($request->prov_domisili);
                $jsonProvDomisili = $prov_domisili->getContent();
                $data = json_decode($jsonProvDomisili, true);
                $provDomisili = $data['nama'];

                    // kabkot domisili
                    $kabkot_domisili = $this->detailKabKot($request->kabkot_domisili);
                    $jsonKabkotDomisili = $kabkot_domisili->getContent();
                    $data = json_decode($jsonKabkotDomisili, true);
                    $kabkotDomisili = $data['nama'];

                    // kec domisili
                    $kec_domisili = $this->detailKec($request->kec_domisili);
                    $jsonKecDomisili = $kec_domisili->getContent();
                    $data = json_decode($jsonKecDomisili, true);
                    $kecDomisili = $data['nama'];

                    // kel domisili
                    $kel_domisili = $this->detailKel($request->kel_domisili);
                    $jsonKelDomisili = $kel_domisili->getContent();
                    $data = json_decode($jsonKelDomisili, true);
                    $kelDomisili = $data['nama'];   
                
                $final_domisili = $request->alamat_domisili.", Kelurahan ".$kelDomisili.", Kecamatan ".$kecDomisili.", ".$kabkotDomisili.", ".$provDomisili;
            } else {
                $final_domisili = $user->alamat_domisili;
            }

            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            $user->nik = $request->nik ?? $user->nik;
            $user->tgl_lahir = $request->tgl_lahir ?? $user->tgl_lahir;
            $user->tempat_lahir = $kabkot_lahir;
            $user->kelamin = $request->kelamin ?? $user->kelamin;
            $user->status_perkawinan = $request->status_perkawinan ?? $user->status_perkawinan;
            $user->jlh_tk = $request->jlh_tk ?? $user->jlh_tk;
            $user->noHP = $request->noHP ?? $user->noHP;
            $user->pendidikan = $request->pendidikan ?? $user->pendidikan;
            $user->jurusan = $request->jurusan ?? $user->jurusan;
            $user->agama = $request->agama ?? $user->agama;
            $user->npwp = $request->npwp ?? $user->npwp;
            $user->domisili = $request->domisili ?? $user->domisili;
            $user->alamat_ktp = $final_ktp;
            $user->alamat_domisili = $final_domisili;
            $user->training = $request->training ?? $user->training;
            $user->nrk = $request->nrk ?? $user->nrk;
            $user->kantor = $request->kantor ?? $user->kantor;
            $user->jabatan = $request->jabatan ?? $user->jabatan;
            $user->divisi = $request->divisi ?? $user->divisi;
            $user->departemen = $request->departemen ?? $user->departemen;
            $user->status_karyawan = $request->status_karyawan ?? $user->status_karyawan;
            $user->grade = $request->grade ?? $user->grade;
            $user->bpjs_kesehatan = $request->bpjs_kesehatan ?? $user->bpjs_kesehatan;
            $user->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan ?? $user->bpjs_ketenagakerjaan;
            $user->tgl_masuk = $request->tgl_masuk ?? $user->tgl_masuk;
            $user->tgl_keluar = $request->tgl_sampai ?? $user->tgl_keluar;
            $user->masa_kerja = $request->masa_kerja ?? $user->masa_kerja;
            $user->no_rekening = $request->norek ?? $user->no_rekening;
            $user->rekening = $request->rek ?? $user->rekening;
            $user->faskes_1 = $request->faskes1 ?? $user->faskes_1;
            $user->gaji_pokok = $request->gaji ?? $user->gaji_pokok;
            $user->tunjangan_tetap = $request->tunjangan_t ?? $user->tunjangan_tetap;
            $user->tunjangan_tidak_tetap = $request->tunjangan_tt ?? $user->tunjangan_tidak_tetap;
            $user->keterangan = $request->keterangan ?? $user->keterangan;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $originalName = $file->getClientOriginalName();
                $newName = time() . '_' . str_replace(' ', '_', $originalName);
                $request->foto->move('storage/upload/profil/', $newName );
                $user->foto = $newName;
            }
            
            if ($request->hasFile('signature')) {
                $file = $request->file('signature');
                $originalName = $file->getClientOriginalName();
                $newName = time() . '_' . str_replace(' ', '_', $originalName);
                $request->signature->move('storage/upload/ttd/', $newName );
                $user->signature = $newName;
            }
            
            $user->bio = $request->bio ?? $user->bio;
            $user->updated_at = Carbon::now();

            $user->save();

            return response()->json([
                'data' => $user,
                'message' => 'Data Updated Successfully.',
                'code' => 200,
                'success' => true
            ], 200);
        } catch (QueryException $ex) {
            return response()->json([
                'message' => 'Failed to update data',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }

    public function reset_password(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ], 400);
            }
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'Can`t find Email in record.',
                    'code' => 409,
                    'success' => false
                ], 409);
            }
            Mail::to($user->email)->send(new ResetPassword($user));
            return response()->json([
                'message' => 'Reset Password Link sent to Email.',
                'code' => 200,
                'success' => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to reset password',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }

    public function update_password(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors(),
                    'code' => 400,
                    'success' => false
                ], 400);
            }
            $data = User::where('email', $request->email)->first();

            $data->update([
                'password' => Hash::make($request->password),
                'updated_at'    => Carbon::now()
            ]);

            return response()->json([
                'data' => new $data,
                'message' => 'Data Updated Successfully',
                'code' => 200,
                'success' => true
            ],200);
        }catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Something went wrong',
                'code' => 500,
                'success' => false
            ], 500);
        }
    }
}
