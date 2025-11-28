<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\SubmitableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Relations\BelongsToRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Schema\Types\Wiki\VideoType;
use App\Pivots\Wiki\AnimeThemeEntryVideo;

class AnimeThemeEntryVideoType extends PivotType implements SubmitableType
{
    public function description(): string
    {
        return 'Represents the association between an anime theme entry and a video.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeThemeEntryType(), AnimeThemeEntryVideo::RELATION_ENTRY)
                ->notNullable(),
            new BelongsToRelation(new VideoType(), AnimeThemeEntryVideo::RELATION_VIDEO)
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
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
