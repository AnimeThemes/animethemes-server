<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\Request;

/**
 * Class SynonymCollection.
 */
class SynonymCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animesynonyms';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = AnimeSynonym::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(
            fn (AnimeSynonym $synonym) => SynonymResource::make($synonym, $this->query)
        )->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new SynonymSchema();
    }
}
