<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Models\Wiki\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ImageResource.
 *
 * @mixin Image
 */
class ImageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'image';

    /**
     * Create a new resource instance.
     *
     * @param Image | MissingValue | null $image
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Image | MissingValue | null $image, QueryParser $parser)
    {
        parent::__construct($image, $parser);
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
            'id' => $this->when($this->isAllowedField('id'), $this->image_id),
            'path' => $this->when($this->isAllowedField('path'), $this->path),
            'size' => $this->when($this->isAllowedField('size'), $this->size),
            'mimetype' => $this->when($this->isAllowedField('mimetype'), $this->mimetype),
            'facet' => $this->when($this->isAllowedField('facet'), $this->facet?->description),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'link' =>  $this->when($this->isAllowedField('link'), route('image.show', $this)),
            'artists' => ArtistCollection::make($this->whenLoaded('artists'), $this->parser),
            'anime' => AnimeCollection::make($this->whenLoaded('anime'), $this->parser),
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
            'artists',
        ];
    }
}
