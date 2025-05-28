<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Admin;

use App\GraphQL\Definition\Fields\Admin\FeaturedTheme\FeaturedThemeEndAtField;
use App\GraphQL\Definition\Fields\Admin\FeaturedTheme\FeaturedThemeStartAtField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeTheme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\Models\Admin\FeaturedTheme;

/**
 * Class FeaturedThemeType.
 */
class FeaturedThemeType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a video to be featured on the homepage of the site for a specified amount of time.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeThemeEntryType(), FeaturedTheme::RELATION_ENTRY, nullable: false),
            new BelongsToRelation(new VideoType(), FeaturedTheme::RELATION_VIDEO, nullable: false),
            new BelongsToRelation(new UserType(), FeaturedTheme::RELATION_USER),
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
            new IdField(FeaturedTheme::ATTRIBUTE_ID),
            new FeaturedThemeStartAtField(),
            new FeaturedThemeEndAtField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
