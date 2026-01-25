<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingrediente extends Model
{
    protected $table = 'ingredientes';

    protected $fillable = [
        'nombre',
        'unidad',
    ];

    public function recetas(): HasMany
    {
        return $this->hasMany(Receta::class, 'ingrediente_id');
    }
}
