<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SynonymResource.
 */
class SynonymResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animesynonym';

    /**
     * Create a new resource instance.
     *
     * @param  AnimeSynonym | MissingValue | null  $synonym
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(AnimeSynonym|MissingValue|null $synonym, ReadQuery $query)
    {
        parent::__construct($synonym, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeSynonym::RELATION_ANIME] = new AnimeResource($this->whenLoaded(AnimeSynonym::RELATION_ANIME), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new SynonymSchema();
    }
}
