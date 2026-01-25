<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receta extends Model
{
    protected $table = 'recetas';

    protected $fillable = [
        'producto_id',
        'ingrediente_id',
        'cantidad',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function ingrediente(): BelongsTo
    {
        return $this->belongsTo(Ingrediente::class, 'ingrediente_id');
    }
}
