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
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
