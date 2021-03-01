<?php

namespace Webkul\Exchange\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Routing\Router;
use Webkul\Admin\Http\Middleware\Locale;

class ExchangeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Http/admin-routes.php');

        $this->loadRoutesFrom(__DIR__ . '/../Http/shop-routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'admin');
        $this->publishes([__DIR__.'/../Resources/lang' => resource_path('lang/vendor/admin')]);

        // $this->publishes([
        //     __DIR__ . '/../../publishable/assets' => public_path('themes/default/assets'),
        // ], 'public');
        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/exchange/assets'),
        ], 'public');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'exchange');

        Event::listen('bagisto.admin.layout.head', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('exchange::admin.layouts.style');
        });

        $router->aliasMiddleware('admin_locale', Locale::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }
}