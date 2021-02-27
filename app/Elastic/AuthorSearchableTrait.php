<?php

namespace App\Elastic;

use ScoutElastic\Searchable;

use Illuminate\Support\Arr;

trait AuthorSearchableTrait
{
    use Searchable;

    protected $indexConfigurator = AuthorIndexConfigurator::class;

    // Here you can specify a mapping for model fields
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
                'analyzer' => 'name_analyzer',
            ]
        ]
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array;
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Preserve only attributes that are meant to be searched in
        $searchable = Arr::only($array, ['name', 'description']);

        return $searchable;
    }
}
