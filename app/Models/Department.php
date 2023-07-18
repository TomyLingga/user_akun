<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'divisi_id',
        'department',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function division()
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }
}
