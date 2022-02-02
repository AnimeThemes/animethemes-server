<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Query\Query;
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(AnimeSynonym|MissingValue|null $synonym, Query $query)
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
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            AnimeSynonym::ATTRIBUTE_TEXT => $this->when($this->isAllowedField(AnimeSynonym::ATTRIBUTE_TEXT), $this->text),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            AnimeSynonym::RELATION_ANIME => AnimeResource::make($this->whenLoaded(AnimeSynonym::RELATION_ANIME), $this->query),
        ];
    }
}
