<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Song;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceAliasField;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceAsField;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceMemberAliasField;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceMemberAsField;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceRelevanceField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\GraphQL\Schema\Types\Wiki\SongType;
use App\Models\Wiki\Song\Performance;

class PerformanceType extends EloquentType
{
    public function description(): string
    {
        return 'Represents the link between a song and an artist or group.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(Performance::ATTRIBUTE_ID, Performance::class),
            new PerformanceAliasField(),
            new PerformanceAsField(),
            new PerformanceMemberAliasField(),
            new PerformanceMemberAsField(),
            new PerformanceRelevanceField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new BelongsToRelation(new SongType(), Performance::RELATION_SONG)
                ->nonNullable(),
            new BelongsToRelation(new ArtistType(), Performance::RELATION_ARTIST)
                ->nonNullable(),
            new BelongsToRelation(new ArtistType(), Performance::RELATION_MEMBER),
        ];
    }
}
