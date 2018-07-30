<?php

namespace App\Modules\Dashboard\Providers;

use App\Helpers\NavHelper;
use App\Helpers\ViewHelper;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('NavHelper', function()
        {
            return new NavHelper($this->app->request);
        });

        $this->app->bind('ViewHelper', function()
        {
            return new ViewHelper($this->app->request);
        });
    }
}
