<?php

namespace App\Filament\Vendedor\Widgets;

use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PedidosActivosWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $activos = Pedido::whereIn('estado', ['nuevo', 'en_produccion', 'listo'])
            ->count();
        
        // Calcular tendencia comparando con la semana anterior
        $semanaAnterior = Pedido::whereIn('estado', ['nuevo', 'en_produccion', 'listo'])
            ->whereBetween('created_at', [Carbon::now()->subWeeks(2), Carbon::now()->subWeek()])
            ->count();
        
        $diferencia = $activos - $semanaAnterior;
        $tendencia = $semanaAnterior > 0 ? round(($diferencia / $semanaAnterior) * 100) : 0;
        
        return [
            Stat::make('Pedidos Activos', $activos)
                ->description($diferencia >= 0 ? "+{$diferencia} vs semana anterior" : "{$diferencia} vs semana anterior")
                ->descriptionIcon($diferencia >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([7, 4, 8, 5, 3, 5, $activos])
                ->color($diferencia >= 0 ? 'success' : 'warning')
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }
}
