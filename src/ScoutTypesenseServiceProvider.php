<?php

namespace Aionteck\ScoutTypesense;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class ScoutTypesenseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        resolve(EngineManager::class)->extend('typesense', function () {
            return new TypesenseEngine();
        });
    }
}