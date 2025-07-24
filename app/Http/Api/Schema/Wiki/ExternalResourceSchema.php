<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\InteractsWithPivots;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceIdField;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceLinkField;
use App\Http\Api\Field\Wiki\ExternalResource\ExternalResourceSiteField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Wiki\AnimeResourceSchema;
use App\Http\Api\Schema\Pivot\Wiki\ArtistResourceSchema;
use App\Http\Api\Schema\Pivot\Wiki\SongResourceSchema;
use App\Http\Api\Schema\Pivot\Wiki\StudioResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Http\Resources\Pivot\Wiki\Resource\StudioResourceResource;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;

class ExternalResourceSchema extends EloquentSchema implements InteractsWithPivots
{
    /**
     * Get the allowed pivots of the schema.
     *
     * @return AllowedInclude[]
     */
    public function allowedPivots(): array
    {
        return [
            new AllowedInclude(new AnimeResourceSchema(), AnimeResourceResource::$wrap),
            new AllowedInclude(new ArtistResourceSchema(), ArtistResourceResource::$wrap),
            new AllowedInclude(new StudioResourceSchema(), StudioResourceResource::$wrap),
            new AllowedInclude(new SongResourceSchema(), SongResourceResource::$wrap),
        ];
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ExternalResourceResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), ExternalResource::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), ExternalResource::RELATION_ARTISTS),
            new AllowedInclude(new SongSchema(), ExternalResource::RELATION_SONGS),
            new AllowedInclude(new StudioSchema(), ExternalResource::RELATION_STUDIOS),
        ]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, ExternalResource::ATTRIBUTE_ID),
                new ExternalResourceIdField($this),
                new ExternalResourceLinkField($this),
                new ExternalResourceSiteField($this),
            ],
        );
    }
}
