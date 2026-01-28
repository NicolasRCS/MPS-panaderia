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
                'primary' => [
                    50 => '243, 244, 246',
                    100 => '229, 231, 235',
                    200 => '209, 213, 219',
                    300 => '156, 163, 175',
                    400 => '107, 114, 128',
                    500 => '75, 85, 99',
                    600 => '55, 65, 81',
                    700 => '31, 41, 55',
                    800 => '17, 24, 39',
                    900 => '3, 7, 18',
                    950 => '1, 2, 4',
                ],
                'gray' => Color::Zinc,
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
            ->brandName('Il Molino')
            ->brandLogo(null)
            ->brandLogoHeight('4rem')
            ->darkModeBrandLogo(null)
            ->favicon(asset('images/logo-il-molino-1.png'))
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::sidebar.start',
                fn (): string => view('filament.vendedor.components.sidebar-logo')->render()
            )
            ->navigationGroups([])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}