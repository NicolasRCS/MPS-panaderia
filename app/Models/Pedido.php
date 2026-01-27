<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_id',
        'fecha',
        'fecha_carga',
        'fecha_realizacion',
        'producto_id',
        'cantidad',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_carga' => 'date',
        'fecha_realizacion' => 'date',
    ];

    /**
     * Relación: Un pedido pertenece a un cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relación: Un pedido pertenece a un producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Relación: Un pedido puede estar asociado a muchas órdenes de producción
     */
    public function ordenesProduccion()
    {
        return $this->belongsToMany(OrdenProduccion::class, 'pedido_orden_produccion', 'pedido_id', 'orden_produccion_id');
    }
}
