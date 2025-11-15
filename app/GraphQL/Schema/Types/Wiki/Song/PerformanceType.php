<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Song;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceAliasField;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceAsField;
use App\GraphQL\Schema\Fields\Wiki\Song\Performance\PerformanceRelevanceField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\SongType;
use App\GraphQL\Schema\Unions\PerformanceArtistUnion;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\MorphToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Song\Performance;

class PerformanceType extends EloquentType implements ReportableType
{
    public function description(): string
    {
        return "Represents the link between a song and an artist or membership.\n\nFor example, Liella has performed using memberships, with its members credited.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new SongType(), Performance::RELATION_SONG)
                ->notNullable(),
            new MorphToRelation(new PerformanceArtistUnion(), Performance::RELATION_ARTIST)
                ->notNullable(),
        ];
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
            new PerformanceRelevanceField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
