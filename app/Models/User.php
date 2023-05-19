<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
        'noHP',
        'foto',
        'bio',
        'klinik_id',
        'jabatan',
        'departemen',
        'divisi',
        'nrk',
        'nik',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'tgl_masuk',
        'tgl_keluar',
        'masa_kerja',
        'tempat_lahir',
        'tgl_lahir',
        'jlh_tk',
        'kelamin',
        'status_karyawan',
        'grade',
        'pendidikan',
        'jurusan',
        'agama',
        'kantor',
        'keterangan',
        'status_perkawinan',
        'alamat_ktp',
        'alamat_domisili',
        'domisili',
        'npwp',
        'training',
        'faskes_1',
        'rekening',
        'no_rekening',
        'gaji_pokok',
        'tunjangan_tetap',
        'tunjangan_tidak_tetap',
        'signature'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function akses()
    {
        return $this->hasMany(MasterAkses::class, 'user_id');
    }
}
