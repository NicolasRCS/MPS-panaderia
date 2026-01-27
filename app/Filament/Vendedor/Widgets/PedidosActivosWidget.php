<?php

namespace App\Filament\Vendedor\Widgets;

use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget: Pedidos Activos
 * 
 * Muestra la cantidad total de pedidos que no están completados
 * 
 * PENDIENTE DE IMPLEMENTAR:
 * - Lógica para contar pedidos activos basándose en el campo 'estado'
 * - Definir qué estados se consideran "activos"
 * - Agregar iconos y colores apropiados
 * - Considerar agregar tendencia (comparación con período anterior)
 */
class PedidosActivosWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // TODO: Implementar la lógica de conteo
        // Ejemplo: $activos = Pedido::whereIn('estado', ['nuevo', 'en_produccion'])->count();
        
        return [
            Stat::make('Pedidos Activos', 0)
                ->description('Pedidos pendientes de completar')
                ->descriptionIcon('heroicon-o-clock')
                ->color('primary'),
        ];
    }
}
