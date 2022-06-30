<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Pivots\AnimeImage;
use App\Pivots\BasePivot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnimeImageResource.
 *
 * @mixin AnimeImage
 */
class AnimeImageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animeimage';

    /**
     * Create a new resource instance.
     *
     * @param  AnimeImage | MissingValue | null  $pivot
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(AnimeImage|MissingValue|null $pivot, ReadQuery $query)
    {
        parent::__construct($pivot, $query);
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

        if ($this->isAllowedField(BasePivot::ATTRIBUTE_CREATED_AT)) {
            $result[BasePivot::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BasePivot::ATTRIBUTE_UPDATED_AT)) {
            $result[BasePivot::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        $result[AnimeImage::RELATION_ANIME] = new AnimeResource($this->whenLoaded(AnimeImage::RELATION_ANIME), $this->query);
        $result[AnimeImage::RELATION_IMAGE] = new ImageResource($this->whenLoaded(AnimeImage::RELATION_IMAGE), $this->query);

        return $result;
    }
}
