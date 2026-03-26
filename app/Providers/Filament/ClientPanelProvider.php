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
            ->brandLogo(asset('assets/hannah_logo.png'))
            ->darkModeBrandLogo(asset('assets/hannah_logo.png'))
            ->brandLogoHeight('6rem')
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
                PanelsRenderHook::HEAD_END,
                function () {
                    if (! request()->routeIs('filament.clientes.auth.login')) {
                        return '';
                    }

                    return new HtmlString('
                        <style>
                            body {
                                background-image:
                                    linear-gradient(rgba(0, 0, 0, 0.26), rgba(0, 0, 0, 0.36)),
                                    url("' . asset('assets/client_bg.jpg') . '");
                                background-size: cover;
                                background-position: center;
                                background-repeat: no-repeat;
                                background-attachment: fixed;
                            }

                            .fi-simple-main-ctn,
                            .fi-simple-main {
                                animation: loginCardEnter 520ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
                            }

                            .fi-simple-main {
                                box-shadow: 0 24px 56px -26px rgba(0, 0, 0, 0.55) !important;
                            }

                            .fi-simple-main [type="submit"] {
                                transition: transform 180ms ease, box-shadow 220ms ease, filter 220ms ease;
                                box-shadow: 0 12px 30px -14px rgba(0, 0, 0, 0.48);
                                animation: loginButtonPop 540ms ease 180ms both;
                            }

                            .fi-simple-main [type="submit"]:hover {
                                transform: translateY(-1px) scale(1.01);
                                filter: brightness(1.03);
                                box-shadow: 0 18px 34px -16px rgba(0, 0, 0, 0.58);
                            }

                            .fi-simple-main [type="submit"]:active {
                                transform: translateY(0) scale(0.99);
                            }

                            @keyframes loginCardEnter {
                                from {
                                    opacity: 0;
                                    transform: translateY(14px) scale(0.98);
                                }
                                to {
                                    opacity: 1;
                                    transform: translateY(0) scale(1);
                                }
                            }

                            @keyframes loginButtonPop {
                                from {
                                    opacity: 0;
                                    transform: translateY(6px) scale(0.97);
                                }
                                to {
                                    opacity: 1;
                                    transform: translateY(0) scale(1);
                                }
                            }
                        </style>
                    ');
                }
            )
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
