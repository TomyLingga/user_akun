<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class MasterAkses extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'akses_id';

    protected $fillable = [
        'app_id',
        'user_id',
        'level_akses'
    ];

    public function apps() {
        return $this->belongsTo(MasterApps::class, 'app_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
