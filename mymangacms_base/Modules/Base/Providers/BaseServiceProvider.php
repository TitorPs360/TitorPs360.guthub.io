<?php

namespace Modules\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Base\Http\Middleware\AdminMiddleware;
use Modules\Base\Http\Middleware\Authorization;
use Modules\Base\Http\Middleware\InitFront;
use Modules\Base\Http\Middleware\NoAjax;
use Modules\Base\Providers\CollectiveServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    protected $middleware = [
        'auth.admin' => AdminMiddleware::class,
        'permission' => Authorization::class,
        'init' => InitFront::class,
        'noajax' => NoAjax::class,
    ];
    
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->middleware as $name => $class) {
            $this->app['router']->aliasMiddleware($name, $class);
        }
        
        $this->app->register(CollectiveServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('modules\base.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'base'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/permissions.php', 'base.permissions'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/base');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/base';
        }, \Config::get('view.paths')), [$sourcePath]), 'base');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/base');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'base');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'base');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
