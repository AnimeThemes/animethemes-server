<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Filter\Wiki\Synonym\SynonymTextFilter;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\SynonymResource;
use App\Models\Wiki\Synonym;
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
    public static $wrap = 'synonyms';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Synonym::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Synonym $synonym) {
            return SynonymResource::make($synonym, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'anime',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedSortFields(): array
    {
        return [
            'synonym_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'text',
            'anime_id',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return array_merge(
            parent::filters(),
            [
                SynonymTextFilter::class,
            ]
        );
    }
}
