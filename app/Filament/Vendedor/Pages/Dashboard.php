<?php

namespace App\Filament\Vendedor\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Vendedor\Widgets\PedidosActivosWidget;
use App\Filament\Vendedor\Widgets\PedidosHoyWidget;
use App\Filament\Vendedor\Widgets\EstadisticasGeneralesWidget;
use App\Filament\Vendedor\Widgets\PedidosTableWidget;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?string $title = 'Gestión de Pedidos';
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    
    protected static ?int $navigationSort = 1;

    public function getHeaderWidgets(): array
    {
        return [
            PedidosActivosWidget::class,
            PedidosHoyWidget::class,
            EstadisticasGeneralesWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            PedidosTableWidget::class,
        ];
    }
    
    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 3,
            'xl' => 3,
        ];
    }
    
    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 3,
            'xl' => 3,
        ];
    }
}
