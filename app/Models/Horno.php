<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horno extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'capacidad_por_turno',
    ];
}
