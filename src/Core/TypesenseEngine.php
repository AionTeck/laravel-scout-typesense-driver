<?php

namespace Aionteck\LaravelScoutTypesenseDriver\Core;

use Aionteck\LaravelScoutTypesenseDriver\Interfaces\TypesenseDocument;
use Http\Client\Exception;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Engines\Engine;
use Laravel\Scout\Builder;
use Typesense\Client;
use Typesense\Exceptions\TypesenseClientError;

class TypesenseEngine extends Engine
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Model&TypesenseDocument[] $models
     * @return void
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function update($models): void
    {
        foreach ($models as $model) {
            $collectionName = $model->searchableAs();
            $document = $model->toSearchableArray();

            $this->client
                ->getCollections()
                ->offsetGet($collectionName)
                ->getDocuments()
                ->upsert($document);
        }
    }

    /**
     * @param Model&TypesenseDocument $models
     * @return void
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function delete($models): void
    {
        foreach ($models as $model) {
            $this->client
                ->getCollections()
                ->offsetGet($model->searchableAs())
                ->getDocuments()
                ->delete();
        }
    }

    /**
     * @param Builder $builder
     * @return array
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function search(Builder $builder)
    {
        return $this->client
            ->getCollections()
            ->offsetGet($builder->model->searchableAs())
            ->getDocuments()
            ->search([
                'q' => $builder->query,
                'query_by' => implode(',', $builder->model->searchableFields ?? ['*'])
            ]);
    }

    /**
     * @param Builder $builder
     * @param $perPage
     * @param $page
     * @return array
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function paginate(Builder $builder, $perPage, $page = null)
    {
        return $this->client
            ->getCollections()
            ->offsetGet($builder->model->searchableAs())
            ->getDocuments()
            ->search([
                'q' => $builder->query,
                'query_by' => implode(',', $builder->model->searchableFields ?? ['*']),
                'per_page' => $perPage,
                'page' => $page ?? 1,
            ]);
    }

    public function mapIds($results)
    {
        return collect($results['hits'] ?? [])->pluck('document.id');
    }

    public function map(Builder $builder, $results, $model)
    {
        if (! isset($results['hits'])) {
            return collect();
        }

        $ids = collect($results['hits'])->pluck('document.id')->all();

        return $model->whereIn($model->getKeyName(), $ids)->get();
    }

    public function getTotalCount($results): int
    {
        return $results['found'] ?? 0;
    }

    public function lazyMap(Builder $builder, $results, $model)
    {
        return $this->map($builder, $results, $model)->lazy();
    }

    /**
     * @param $model
     * @return void
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function flush($model): void
    {
        $this->client
            ->getCollections()
            ->offsetGet($model->searchableAs())
            ->getDocuments()
            ->delete();
    }

    /**
     * @param $name
     * @param array $options
     * @return void
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function createIndex($name, array $options = []): void
    {
        $this->client->getCollections()->create(array_merge([
            'name' => $name,
            'fields' => [
                ['name' => 'id', 'type' => 'string'],
            ],
        ], $options));
    }

    public function deleteIndex($name)
    {
        $this->client->getCollections()->offsetGet($name)->delete();
    }
}