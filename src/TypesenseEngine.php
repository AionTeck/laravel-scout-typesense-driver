<?php

namespace Aionteck\ScoutTypesense;

use Laravel\Scout\Engines\Engine;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;

class TypesenseEngine extends Engine
{
    public function update($models): void
    {

    }

    public function delete($models): void
    {

    }

    public function search(Builder $builder)
    {

    }

    public function paginate(Builder $builder, $perPage, $page = null)
    {

    }

    public function mapIds($results)
    {

    }

    public function map(Builder $builder, $results, $model)
    {

    }

    public function getTotalCount($result): int
    {
        //Todo
        return 0;
    }
}