<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'deskripsi',
    ];

    public function privileges()
    {
        return $this->belongsToMany(Privilege::class, 'role_privilege');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
