<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\BaseModel;
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(AnimeSynonym::ATTRIBUTE_TEXT)) {
            $result[AnimeSynonym::ATTRIBUTE_TEXT] = $this->text;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        $result[AnimeSynonym::RELATION_ANIME] = AnimeResource::make($this->whenLoaded(AnimeSynonym::RELATION_ANIME), $this->query);

        return $result;
    }
}
