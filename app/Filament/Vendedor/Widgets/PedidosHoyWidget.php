<?php

namespace App\Filament\Vendedor\Widgets;

use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PedidosHoyWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        // Pedidos programados para realizar hoy
        $hoy = Pedido::whereDate('fecha', Carbon::today())
            ->count();
        
        // Pedidos ya listos hoy
        $listos = Pedido::whereDate('fecha', Carbon::today())
            ->where('estado', 'listo')
            ->count();
        
        // Progreso del día
        $progreso = $hoy > 0 ? round(($listos / $hoy) * 100) : 0;
        
        return [
            Stat::make('Pedidos Hoy', $hoy)
                ->description("{$listos} de {$hoy} listos ({$progreso}%)")
                ->descriptionIcon('heroicon-o-calendar-days')
                ->chart([2, 4, 3, 7, 5, 6, $hoy])
                ->color('info')
                ->icon('heroicon-o-clock'),
        ];
    }
}
