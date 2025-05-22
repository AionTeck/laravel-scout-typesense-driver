<?php

namespace Aionteck\LaravelScoutTypesenseDriver\Interfaces;

interface TypesenseDocument
{
    public function searchableAs(): string;

    /**
     * @return string[]
     */
    public function toSearchableArray(): array;
}