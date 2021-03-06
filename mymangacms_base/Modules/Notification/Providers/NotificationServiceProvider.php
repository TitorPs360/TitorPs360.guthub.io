<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Notification\Contracts\NotificationRepository;
use Modules\Notification\Contracts\Notification;
use Modules\User\Contracts\Authentication;
use Modules\Notification\Services\NotificationService;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

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
        $this->app->bind(
            NotificationRepository::class,
            "Modules\\Notification\\Repositories\\NotificationRepositoryImpl"
        );
        $this->app->bind(Notification::class, function ($app) {
            return new NotificationService($app[NotificationRepository::class], $app[Authentication::class]);
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('notification.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'notification'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/permissions.php', 'notification.permissions'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/notification');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/notification';
        }, \Config::get('view.paths')), [$sourcePath]), 'notification');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/notification');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'notification');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'notification');
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
