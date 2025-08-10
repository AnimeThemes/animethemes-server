<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Admin;

use App\GraphQL\Definition\Fields\Admin\FeaturedTheme\FeaturedThemeEndAtField;
use App\GraphQL\Definition\Fields\Admin\FeaturedTheme\FeaturedThemeStartAtField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Admin\FeaturedTheme;

class FeaturedThemeType extends EloquentType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Represents a video to be featured on the homepage of the site for a specified amount of time.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeThemeEntryType(), FeaturedTheme::RELATION_ENTRY)
                ->notNullable(),
            new BelongsToRelation(new VideoType(), FeaturedTheme::RELATION_VIDEO)
                ->notNullable(),
            new BelongsToRelation(new UserType(), FeaturedTheme::RELATION_USER),
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
            new IdField(FeaturedTheme::ATTRIBUTE_ID, FeaturedTheme::class),
            new FeaturedThemeStartAtField(),
            new FeaturedThemeEndAtField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
