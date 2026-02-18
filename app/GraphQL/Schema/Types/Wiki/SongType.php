<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\HasManyRelation;
use App\GraphQL\Schema\Fields\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Fields\Wiki\Song\SongTitleField;
use App\GraphQL\Schema\Fields\Wiki\Song\SongTitleNativeField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Schema\Types\Wiki\Song\PerformanceType;
use App\Models\Wiki\Song;

class SongType extends EloquentType
{
    public function description(): string
    {
        return "Represents the composition that accompanies an AnimeTheme.\n\nFor example, Staple Stable is the song for the Bakemonogatari OP1 AnimeTheme.";
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
            new SongTitleNativeField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new HasManyRelation(new AnimeThemeType(), Song::RELATION_ANIMETHEMES),
            new HasManyRelation(new PerformanceType(), Song::RELATION_PERFORMANCES),
            new MorphToManyRelation($this, new ExternalResourceType(), Song::RELATION_RESOURCES, new ResourceableType()),
        ];
    }
}
