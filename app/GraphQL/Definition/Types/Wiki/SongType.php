<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Song\SongTitleField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Definition\Types\Wiki\Song\PerformanceType;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\MorphToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Song;

class SongType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return "Represents the composition that accompanies an AnimeTheme.\n\nFor example, Staple Stable is the song for the Bakemonogatari OP1 AnimeTheme.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new HasManyRelation(new AnimeThemeType(), Song::RELATION_ANIMETHEMES),
            new HasManyRelation(new PerformanceType(), Song::RELATION_PERFORMANCES),
            new MorphToManyRelation($this, ExternalResourceType::class, Song::RELATION_RESOURCES, ResourceableType::class),
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
            new IdField(Song::ATTRIBUTE_ID, Song::class),
            new SongTitleField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
