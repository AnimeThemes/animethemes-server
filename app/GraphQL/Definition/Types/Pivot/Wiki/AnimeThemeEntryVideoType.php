<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Wiki\AnimeThemeEntryVideo;

class AnimeThemeEntryVideoType extends PivotType implements ReportableType
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
