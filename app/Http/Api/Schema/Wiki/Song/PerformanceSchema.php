<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Song;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Song\Performance\PerformanceAliasField;
use App\Http\Api\Field\Wiki\Song\Performance\PerformanceArtistIdField;
use App\Http\Api\Field\Wiki\Song\Performance\PerformanceArtistTypeField;
use App\Http\Api\Field\Wiki\Song\Performance\PerformanceAsField;
use App\Http\Api\Field\Wiki\Song\Performance\PerformanceSongIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\Wiki\Song\Resource\PerformanceResource;
use App\Models\Wiki\Song\Performance;

/**
 * Class PerformanceSchema.
 */
class PerformanceSchema extends EloquentSchema implements SearchableSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return PerformanceResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return array_merge(
            $this->withIntermediatePaths([
                new AllowedInclude(new SongSchema(), Performance::RELATION_SONG),
            ]),
            []
        );
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
                new IdField($this, Performance::ATTRIBUTE_ID),
                new PerformanceSongIdField($this),
                new PerformanceArtistIdField($this),
                new PerformanceArtistTypeField($this),
                new PerformanceAliasField($this),
                new PerformanceAsField($this),
            ],
        );
    }
}
