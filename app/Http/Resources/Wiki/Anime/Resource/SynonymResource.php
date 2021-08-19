<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SynonymResource.
 *
 * @mixin AnimeSynonym
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
     * @param AnimeSynonym | MissingValue | null $synonym
     * @param Query $query
     * @return void
     */
    public function __construct(AnimeSynonym | MissingValue | null $synonym, Query $query)
    {
        parent::__construct($synonym, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->synonym_id),
            'text' => $this->when($this->isAllowedField('text'), $this->text),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'anime' => AnimeResource::make($this->whenLoaded('anime'), $this->query),
        ];
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
}