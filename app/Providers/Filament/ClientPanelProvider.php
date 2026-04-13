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
            ->registration()
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
                    if (! request()->routeIs('filament.clientes.auth.login') && ! request()->routeIs('filament.clientes.auth.register')) {
                        return '';
                    }

                    return new HtmlString('
                        <style>
                            body {
                                background-image:
                                    linear-gradient(135deg, rgba(7, 12, 23, 0.58), rgba(7, 12, 23, 0.76)),
                                    url("' . asset('assets/client_bg.jpg') . '");
                                background-size: cover;
                                background-position: center;
                                background-repeat: no-repeat;
                                background-attachment: fixed;
                            }

                            body::before {
                                content: "";
                                position: fixed;
                                inset: 0;
                                pointer-events: none;
                                background:
                                    radial-gradient(circle at 12% 18%, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0) 52%),
                                    radial-gradient(circle at 84% 80%, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 45%);
                                z-index: 0;
                            }

                            .fi-simple-main-ctn,
                            .fi-simple-main {
                                animation: loginCardEnter 520ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
                            }

                            .fi-simple-main-ctn {
                                position: relative;
                                z-index: 1;
                            }

                            .fi-simple-main {
                                background: linear-gradient(140deg, rgba(255, 255, 255, 0.30), rgba(255, 255, 255, 0.16)) !important;
                                border: 1px solid rgba(255, 255, 255, 0.34) !important;
                                border-radius: 1.1rem !important;
                                box-shadow:
                                    0 30px 65px -30px rgba(0, 0, 0, 0.72),
                                    inset 0 1px 0 rgba(255, 255, 255, 0.32) !important;
                                backdrop-filter: blur(14px) saturate(130%);
                                -webkit-backdrop-filter: blur(14px) saturate(130%);
                            }

                            .fi-simple-main .fi-simple-header-heading,
                            .fi-simple-main .fi-simple-header-subheading,
                            .fi-simple-main .fi-input-wrp-label,
                            .fi-simple-main .fi-fo-field-wrp-label span,
                            .fi-simple-main .fi-link {
                                color: rgba(255, 255, 255, 0.96) !important;
                                text-shadow: 0 1px 10px rgba(0, 0, 0, 0.30);
                            }

                            .fi-simple-main label,
                            .fi-simple-main .fi-fo-field-wrp-label,
                            .fi-simple-main .fi-fo-field-wrp-label > span,
                            .fi-simple-main .fi-fo-field-label-content,
                            .fi-simple-main .fi-checkbox-list-option-label,
                            .fi-simple-main .fi-fo-checkbox-list-option-label,
                            .fi-simple-main .fi-fo-field-wrp-hint,
                            .fi-simple-main .fi-fo-field-wrp-helper-text,
                            .fi-simple-main .fi-input-wrp-prefix,
                            .fi-simple-main .fi-input-wrp-suffix {
                                color: rgba(248, 250, 252, 0.96) !important;
                            }

                            .fi-simple-main .fi-input-wrp,
                            .fi-simple-main .fi-select-input,
                            .fi-simple-main input {
                                background: rgba(255, 255, 255, 0.23) !important;
                                border-color: rgba(255, 255, 255, 0.34) !important;
                                color: #f8fafc !important;
                            }

                            .fi-simple-main input::placeholder {
                                color: rgba(248, 250, 252, 0.76) !important;
                            }

                            .fi-simple-main .fi-checkbox-input {
                                border-color: rgba(255, 255, 255, 0.45) !important;
                            }

                            .fi-simple-header .fi-logo,
                            .fi-simple-header .fi-logo img {
                                filter: brightness(0) invert(1);
                            }

                            .fi-simple-main [type="submit"] {
                                transition: transform 180ms ease, box-shadow 220ms ease, filter 220ms ease;
                                box-shadow: 0 16px 34px -16px rgba(0, 0, 0, 0.58);
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
