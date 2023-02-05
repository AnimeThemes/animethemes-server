<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Models\Wiki\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ImageResource.
 */
class ImageResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'image';

    /**
     * Create a new resource instance.
     *
     * @param  Image | MissingValue | null  $image
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Image|MissingValue|null $image, ReadQuery $query)
    {
        parent::__construct($image, $query);
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

        $result[Image::RELATION_ARTISTS] = new ArtistCollection($this->whenLoaded(Image::RELATION_ARTISTS), $this->query);
        $result[Image::RELATION_ANIME] = new AnimeCollection($this->whenLoaded(Image::RELATION_ANIME), $this->query);
        $result[Image::RELATION_STUDIOS] = new StudioCollection($this->whenLoaded(Image::RELATION_STUDIOS), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ImageSchema();
    }
}
