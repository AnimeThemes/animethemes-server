<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Performance\PerformanceAliasField;
use App\Http\Api\Field\Wiki\Performance\PerformanceArtistIdField;
use App\Http\Api\Field\Wiki\Performance\PerformanceAsField;
use App\Http\Api\Field\Wiki\Performance\PerformanceMemberAliasField;
use App\Http\Api\Field\Wiki\Performance\PerformanceMemberAsField;
use App\Http\Api\Field\Wiki\Performance\PerformanceMemberIdField;
use App\Http\Api\Field\Wiki\Performance\PerformanceRelevanceField;
use App\Http\Api\Field\Wiki\Performance\PerformanceSongIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\PerformanceJsonResource;
use App\Models\Wiki\Performance;

class PerformanceSchema extends EloquentSchema implements SearchableSchema
{
    public function type(): string
    {
        return PerformanceJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new SongSchema(), Performance::RELATION_SONG),
            new AllowedInclude(new ArtistSchema(), Performance::RELATION_ARTIST),
            new AllowedInclude(new ArtistSchema(), Performance::RELATION_MEMBER),
        ]);
    }

    /**
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
                new PerformanceMemberIdField($this),
                new PerformanceAliasField($this),
                new PerformanceAsField($this),
                new PerformanceMemberAliasField($this),
                new PerformanceMemberAsField($this),
                new PerformanceRelevanceField($this),
            ],
        );
    }
}
