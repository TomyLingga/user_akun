<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'divisi',
        'bom',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function departments()
    {
        return $this->hasMany(Departement::class, 'divisi_id');
    }
}
