<?php

namespace Aionteck\LaravelScoutTypesenseDriver\Provides;

use Aionteck\LaravelScoutTypesenseDriver\Core\TypesenseEngine;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Typesense\Client;

class ScoutTypesenseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TypesenseEngine::class, function (App $app) {
            $config = $app->make(Config::class);

            return new TypesenseEngine(
                new Client($config->get('scout.typesense'))
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