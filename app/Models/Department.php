<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'departments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'divisi_id',
        'department',
        'kode',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function division()
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'departemen');
    }
}
