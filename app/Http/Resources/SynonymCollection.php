<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\PerformsResourceCollectionQuery;
use App\JsonApi\Traits\PerformsResourceCollectionSearch;

class SynonymCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery, PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'synonyms';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($synonym) {
            return SynonymResource::make($synonym, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static function allowedIncludePaths()
    {
        return [
            'anime',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static function allowedSortFields()
    {
        return [
            'synonym_id',
            'created_at',
            'updated_at',
            'text',
            'anime_id',
        ];
    }
}