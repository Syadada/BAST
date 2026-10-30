<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'module',
        'deskripsi',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_privilege');
    }
}
