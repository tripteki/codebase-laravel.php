<?php

namespace Src\V1\Web\Providers\Filament;

use Filament\PanelProvider;
use Filament\Panel;
use Src\V1\Web\Filament\Pages\Dashboard;
use Filament\View\PanelsRenderHook;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @param \Filament\Panel $panel
     * @return \Filament\Panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
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

        ->login()

        ->id("admin")
        ->path("admin")
        ->viteTheme("resources/css/filament/admin/theme.css")
        ->favicon(asset("asset/favicon.png"))
        ->brandName(Str::title(config("app.name")))
        ->brandLogo(asset("asset/brand-light.png"))
        ->darkModeBrandLogo(asset("asset/brand-dark.png"))
        ->colors([

            "primary" => Color::Blue,
        ])
        ->widgets([

            //
        ])
        ->renderHook(
            PanelsRenderHook::FOOTER,
            fn () => view("vendor.filament.footer")
        )
        ->pages([

            Dashboard::class,
        ])

        ->databaseTransactions()
        ->databaseNotifications()
        ->databaseNotificationsPolling("5s")
        ->default()
        ->spa()
        ->sidebarCollapsibleOnDesktop()

        ->discoverResources(

            in: base_path("src/V1/Web/Filament/Resources"),
            for: "Src\\V1\\Web\\Filament\\Resources"
        )
        ->discoverPages(

            in: base_path("src/V1/Web/Filament/Pages"),
            for: "Src\\V1\\Web\\Filament\\Pages"
        )
        ->discoverWidgets(

            in: base_path("src/V1/Web/Filament/Widgets"),
            for: "Src\\V1\\Web\\Filament\\Widgets"
        );
    }
}
