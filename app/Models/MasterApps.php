<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class MasterApps extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'app_id';

    protected $fillable = [
        'nama_app',
        'url_app',
        'logo_app',
        'status_app'
    ];

    public function akses()
    {
        return $this->hasMany(MasterAkses::class, 'app_id');
    }
}
