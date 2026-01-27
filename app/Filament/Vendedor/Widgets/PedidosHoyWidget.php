<?php

namespace App\Filament\Vendedor\Widgets;

use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

/**
 * Widget: Pedidos Hoy
 * 
 * Muestra la cantidad de pedidos para el día de hoy
 * 
 * PENDIENTE DE IMPLEMENTAR:
 * - Lógica para contar pedidos de hoy basándose en el campo 'fecha'
 * - Agregar iconos y colores apropiados
 * - Considerar mostrar el valor total de pedidos de hoy
 */
class PedidosHoyWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // TODO: Implementar la lógica de conteo
        // Ejemplo: $hoy = Pedido::whereDate('fecha', Carbon::today())->count();
        
        return [
            Stat::make('Pedidos Hoy', 0)
                ->description('Pedidos programados para hoy')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),
        ];
    }
}
