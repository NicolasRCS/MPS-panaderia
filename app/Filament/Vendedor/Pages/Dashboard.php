<?php

namespace App\Filament\Vendedor\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/**
 * Dashboard principal del Vendedor
 * 
 * Características:
 * - Visualización de widgets con estadísticas rápidas (pedidos activos, pedidos hoy)
 * - Acceso rápido a creación de nuevos pedidos
 * - Vista resumida de pedidos recientes
 * 
 * PENDIENTE DE IMPLEMENTAR:
 * - Configurar widgets en getHeaderWidgets()
 * - Agregar widgets personalizados en la carpeta Widgets/
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?string $title = 'Gestión de Pedidos';

    /**
     * Widgets que se mostrarán en la parte superior del dashboard
     * 
     * TODO: Agregar los widgets cuando estén implementados:
     * - PedidosActivosWidget::class
     * - PedidosHoyWidget::class
     * - EstadisticasSemanalesWidget::class (opcional)
     */
    public function getHeaderWidgets(): array
    {
        return [
            // Aquí se agregarán los widgets
        ];
    }

    /**
     * Widgets que se mostrarán en el cuerpo del dashboard
     * 
     * TODO: Agregar widgets para:
     * - Lista de pedidos recientes
     * - Gráficos de estadísticas
     */
    public function getWidgets(): array
    {
        return [
            // Aquí se agregarán más widgets si es necesario
        ];
    }
}
