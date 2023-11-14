<?php
		
		namespace App\Providers\Filament;
		
		use App\Filament\Pages\EditProfile;
		use Filament\Http\Middleware\Authenticate;
		use Filament\Http\Middleware\DisableBladeIconComponents;
		use Filament\Http\Middleware\DispatchServingFilamentEvent;
		use Filament\Navigation\MenuItem;
		use Filament\Pages;
		use Filament\Panel;
		use Filament\PanelProvider;
		use Filament\Support\Colors\Color;
		use Filament\Widgets;
		use Hasnayeen\Themes\Http\Middleware\SetTheme;
		use Hasnayeen\Themes\ThemesPlugin;
		use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
		use Illuminate\Cookie\Middleware\EncryptCookies;
		use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
		use Illuminate\Routing\Middleware\SubstituteBindings;
		use Illuminate\Session\Middleware\AuthenticateSession;
		use Illuminate\Session\Middleware\StartSession;
		use Illuminate\View\Middleware\ShareErrorsFromSession;
		use lockscreen\FilamentLockscreen\Http\Middleware\Locker;
		use lockscreen\FilamentLockscreen\Lockscreen;
		class AdminPanelProvider extends PanelProvider
		{
				
				public function panel( Panel $panel ) : Panel
				{
						return $panel
								->default()
								->id( 'admin' )
								->path( 'admin' )
								->login()
								->profile()
								->colors( [
										'danger'  => Color::Rose,
										'gray'    => Color::Gray,
										'info'    => Color::Blue,
										'primary' => Color::Indigo,
										'success' => Color::Emerald,
										'warning' => Color::Orange,
								] )
								/*      ->font( 'Monaco' )*/
								->discoverResources( in: app_path( 'Filament/Resources' ), for: 'App\\Filament\\Resources' )
								->discoverPages( in: app_path( 'Filament/Pages' ), for: 'App\\Filament\\Pages' )
								->pages( [
										Pages\Dashboard::class,
								] )
								->discoverWidgets( in: app_path( 'Filament/Widgets' ), for: 'App\\Filament\\Widgets' )
								->widgets( [
										/*  Widgets\AccountWidget::class,
												Widgets\FilamentInfoWidget::class,*/
								] )
								->databaseNotifications()
								->plugins(
										[
												Lockscreen::make(),
												ThemesPlugin::make()
										]
								)
								->middleware( [
										EncryptCookies::class,
										AddQueuedCookiesToResponse::class,
										StartSession::class,
										AuthenticateSession::class,
										ShareErrorsFromSession::class,
										VerifyCsrfToken::class,
										SubstituteBindings::class,
										DisableBladeIconComponents::class,
										DispatchServingFilamentEvent::class,
										SetTheme::class
								] )
								->authMiddleware( [
										Authenticate::class,
										Locker::class, // <- Add this
								] );
				}
				
		}
