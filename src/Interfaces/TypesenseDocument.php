<?php

namespace Aionteck\LaravelScoutTypesenseDriver\Interfaces;

interface TypesenseDocument
{
    /**
     * @return string
     */
    public function searchableAs();

    /**
     * @return string[]
     */
    public function toSearchableArray(): array;
}