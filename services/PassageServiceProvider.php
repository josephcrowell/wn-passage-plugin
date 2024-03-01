<?php
namespace JosephCrowell\Passage\Services;

use Winter\Storm\Support\ServiceProvider;

class PassageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("PassageService", function ($app)
        {
            return new \JosephCrowell\Passage\Classes\PermissionsService();
        });
    }
}
