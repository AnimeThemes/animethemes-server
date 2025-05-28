<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeTheme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\Pivots\Wiki\AnimeThemeEntryVideo;

/**
 * Class AnimeThemeEntryVideoType.
 */
class AnimeThemeEntryVideoType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the association between an anime theme entry and a video.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeThemeEntryType(), AnimeThemeEntryVideo::RELATION_ENTRY, nullable: false),
            new BelongsToRelation(new VideoType(), AnimeThemeEntryVideo::RELATION_VIDEO, nullable: false),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
