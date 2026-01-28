<?php

namespace App\Filament\Vendedor\Widgets;

use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class EstadisticasGeneralesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        // Total de pedidos este mes
        $totalMes = Pedido::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        return [
            Stat::make('Pedidos del Mes', $totalMes)
                ->description('Total de pedidos en ' . Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-o-calendar')
                ->chart([12, 15, 18, 22, 19, 25, $totalMes])
                ->color('primary')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
