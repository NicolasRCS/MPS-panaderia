<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'unidad',
        'stock_inicial',
        'stock_minimo',
        'tamano_lote',
        'capacidad_por_turno',
    ];

    public function recetas(): HasMany
    {
        return $this->hasMany(Receta::class, 'producto_id');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'producto_id');
    }
}
