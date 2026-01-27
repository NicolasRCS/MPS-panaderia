<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenProduccion extends Model
{
    protected $table = 'ordenes_produccion';


    protected $fillable = [
        'producto_id',
        'cantidad',
        'estado',
    ];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_orden_produccion', 'orden_produccion_id', 'pedido_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Crea una orden de producción agrupando pedidos por producto y fecha.
     * @param int $productoId
     * @param string $fecha
     * @param \Illuminate\Support\Collection|array $pedidos
     * @return self
     */
    public static function crearAgrupada($productoId, $fecha, $pedidos)
    {
        $cantidadTotal = collect($pedidos)->sum('cantidad');
        $orden = self::create([
            'producto_id' => $productoId,
            'cantidad' => $cantidadTotal,
            'estado' => 'pendiente',
        ]);
        $orden->pedidos()->attach(collect($pedidos)->pluck('id'));
        return $orden;
    }
}
