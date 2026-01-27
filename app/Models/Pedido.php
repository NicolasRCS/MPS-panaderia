<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'fecha',
        'producto_id',
        'cantidad',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function ordenesProduccion()
    {
        return $this->belongsToMany(OrdenProduccion::class, 'pedido_orden_produccion', 'pedido_id', 'orden_produccion_id');
    }
}
