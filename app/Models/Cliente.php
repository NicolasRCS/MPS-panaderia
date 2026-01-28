<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'numero_pedido', // Número de pedido del cliente (ej: PED001, PED002)
        'nombre', // Nombre completo del cliente
        'telefono', // Teléfono del cliente
        'direccion', // Dirección del cliente
        'observaciones', // Observaciones o notas sobre el cliente
    ];

    /**
     * Relación: Un cliente puede tener muchos pedidos
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }
}
