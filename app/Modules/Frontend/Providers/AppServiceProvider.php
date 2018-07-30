<?php

namespace App\Modules\Frontend\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->registerTranslations();
        //        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom( __DIR__ . '/../Resources/views', 'frontend');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'frontend');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../$PATH_CONFIG$/config.php' => config_path('$LOWER_NAME$.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../$PATH_CONFIG$/config.php', '$LOWER_NAME$'
        );
    }

}
