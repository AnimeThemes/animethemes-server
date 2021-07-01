<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ExternalResourceResource.
 */
class ExternalResourceResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'resource';

    /**
     * Create a new resource instance.
     *
     * @param ExternalResource | MissingValue | null $resource
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(ExternalResource | MissingValue | null $resource, QueryParser $parser)
    {
        parent::__construct($resource, $parser);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->resource_id),
            'link' => $this->when($this->isAllowedField('link'), $this->link),
            'external_id' => $this->when($this->isAllowedField('external_id'), $this->external_id === null ? '' : $this->external_id),
            'site' => $this->when($this->isAllowedField('site'), strval(optional($this->site)->description)),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('anime_resource', function () {
                return strval($this->pivot->as);
            }, $this->whenPivotLoaded('artist_resource', function () {
                return strval($this->pivot->as);
            }))),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
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
