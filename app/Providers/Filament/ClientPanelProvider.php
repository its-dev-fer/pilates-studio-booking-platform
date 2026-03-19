<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ProcessPendingBooking;
use App\Models\Tenant;
use App\Support\FilamentBrandColors;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('clientes')
            ->path('clientes')
            ->colors(FilamentBrandColors::panelColors())
            ->brandName('Acceso Clientes')
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->tenant(Tenant::class, slugAttribute: 'slug') // Multitenancy también en portal clientes
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\Filament\Client\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\Filament\Client\Pages')
            ->login()
            ->globalSearch(false)
            ->sidebarCollapsibleOnDesktop() // Agrega el botón para colapsar el menú y dejar solo los íconos
            ->sidebarWidth('16rem') // Hace el menú un poco más esbelto y elegante (por defecto es muy ancho)
            ->collapsedSidebarWidth('5rem')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\Filament\Client\Widgets')
            ->widgets([
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
                ProcessPendingBooking::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::TOPBAR_LOGO_AFTER,
                function () {
                    $versionFile = base_path('version.txt');
                    $version = file_exists($versionFile) ? file_get_contents($versionFile) : 'Dev (Local)';
                    // Si NO estamos en producción, mostramos la alerta
                    if (!app()->environment('production')) {
                        return new HtmlString('
                            <div class="ml-3 text-xs text-gray-500 pointer-events-none">
                                v' . trim($version) . '
                            </div>
                        ');
                    }

                    return ''; // En producción no renderiza nada
                }
            );
    }
}
