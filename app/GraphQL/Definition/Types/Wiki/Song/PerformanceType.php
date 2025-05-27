<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Song;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Song\Performance\PerformanceAliasField;
use App\GraphQL\Definition\Fields\Wiki\Song\Performance\PerformanceAsField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\MorphToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\SongType;
use App\GraphQL\Definition\Unions\PerformanceArtistUnion;
use App\Models\Wiki\Song\Performance;

/**
 * Class PerformanceType.
 */
class PerformanceType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the link between a song and an artist or membership.\n\nFor example, Liella has performed using memberships, with its members credited.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new SongType(), Performance::RELATION_SONG),
            new MorphToRelation(new PerformanceArtistUnion(), Performance::RELATION_ARTIST),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array
    {
        return [
            new IdField(Performance::ATTRIBUTE_ID),
            new PerformanceAliasField(),
            new PerformanceAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
