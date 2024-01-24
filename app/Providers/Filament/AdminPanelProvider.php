<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use App\Filament\Pages\Project;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Resources\ProgramResource;
use App\Filament\Resources\ProjectResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => '#4338ca',
                'gray' => '#4338ca',
            ])
            // ->brandLogo(fn () => view('filament.admin.logo'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,

                // NavigationItem::make('Project')
                // ->icon('heroicon-o-inbox-stack')
                // ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.projects.project'))
                // ->url(fn (): string => ProjectResource::getUrl()),


            ])
            // ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            //     return $builder->items([
            //         NavigationItem::make('Dashboard')
            //         ->icon('heroicon-o-home')
            //         ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
            //         ->url(fn (): string => Dashboard::getUrl()),
            //         ...ProgramResource::getNavigationItems(),
            //      NavigationItem::make('Projects')
            //     ->icon('heroicon-o-inbox-stack')
            //     ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.projects.project'))
            //      ->url(route('filament.admin.resources.projects.project')),
            //     ]);
            // })
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->darkMode(false)
             ->collapsibleNavigationGroups(true)
            ->sidebarCollapsibleOnDesktop()
            // ->sidebarFullyCollapsibleOnDesktop()
            // ->sidebarCollapsibleOnDesktop()
            ->spa()
            // ->databaseNotifications()
            // ->databaseNotificationsPolling('30s')
            // ->domain('admin.example.com')
            ->maxContentWidth(MaxWidth::Full)
            //  ->maxContentWidth(MaxWidth::SevenExtraLarge)
            // ->font('Poppins')
            ;
    }
}
