<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Song\SongTitleField;
use App\GraphQL\Schema\Fields\Wiki\Song\SongTitleNativeField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Schema\Types\Wiki\Song\PerformanceType;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\MorphToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Song;

class SongType extends EloquentType implements ReportableType
{
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
            new SongTitleNativeField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
