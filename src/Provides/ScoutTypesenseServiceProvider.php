<?php

namespace Aionteck\LaravelScoutTypesenseDriver\Provides;

use Aionteck\LaravelScoutTypesenseDriver\Core\TypesenseEngine;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Typesense\Client;

class ScoutTypesenseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = $app->make(Config::class);
            return new Client($config->get('scout.typesense'));
        });

        $this->app->singleton(TypesenseEngine::class, function ($app) {
            return new TypesenseEngine(
                $app->make(Client::class)
            );
        });
    }

    public function boot(): void
    {
        $this->app->make(EngineManager::class)->extend('typesense', function () {
            return new TypesenseEngine($this->app->make(Client::class));
        });
    }
}