<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Models\Wiki\Studio;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class StudioResource.
 *
 * @mixin Studio
 */
class StudioResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studio';

    /**
     * Create a new resource instance.
     *
     * @param  Studio | MissingValue | null  $studio
     * @param  Query  $query
     * @return void
     */
    public function __construct(Studio|MissingValue|null $studio, Query $query)
    {
        parent::__construct($studio, $query);
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
            'id' => $this->when($this->isAllowedField('id'), $this->studio_id),
            'name' => $this->when($this->isAllowedField('name'), $this->name),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'anime' => AnimeCollection::make($this->whenLoaded('anime'), $this->query),
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
