<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Panel Provider para el Vendedor
 * 
 * Configuración del panel del vendedor con:
 * - Dashboard personalizado con widgets de estadísticas
 * - Resources: Pedidos, Clientes, Productos (solo lectura)
 * - Navegación ordenada por prioridad
 * 
 * PENDIENTE DE IMPLEMENTAR:
 * - Configurar el dashboard personalizado una vez los widgets estén implementados
 * - Ajustar permisos si se implementa sistema de roles
 */
class VendedorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('vendedor')
            ->path('vendedor')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            // Auto-descubrimiento de Resources (Pedidos, Clientes, Productos)
            ->discoverResources(in: app_path('Filament/Vendedor/Resources'), for: 'App\Filament\Vendedor\Resources')
            
            // Auto-descubrimiento de Pages (Dashboard personalizado)
            ->discoverPages(in: app_path('Filament/Vendedor/Pages'), for: 'App\Filament\Vendedor\Pages')
            
            // Página principal: Dashboard personalizado
            ->pages([
                \App\Filament\Vendedor\Pages\Dashboard::class,
            ])
            
            // Auto-descubrimiento de Widgets (PedidosActivosWidget, PedidosHoyWidget)
            ->discoverWidgets(in: app_path('Filament/Vendedor/Widgets'), for: 'App\Filament\Vendedor\Widgets')
            
            // Widgets globales
            ->widgets([
                // AccountWidget::class, // Descomentado si se desea mostrar
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandName('Il Molino - Vendedor')
            ->brandLogo(asset('app/Filament/Vendedor/Resources/logo-il-molino-1.png'))
            ->favicon(asset('app/Filament/Vendedor/Resources/logo-il-molino-1.png'));
    }
}

