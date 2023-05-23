<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Admin;

use App\Http\Api\Field\Admin\FeaturedTheme\FeaturedThemeEndAtField;
use App\Http\Api\Field\Admin\FeaturedTheme\FeaturedThemeEntryIdField;
use App\Http\Api\Field\Admin\FeaturedTheme\FeaturedThemeStartAtField;
use App\Http\Api\Field\Admin\FeaturedTheme\FeaturedThemeUserIdField;
use App\Http\Api\Field\Admin\FeaturedTheme\FeaturedThemeVideoIdField;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Auth\UserSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Models\Admin\FeaturedTheme;

/**
 * Class FeaturedThemeSchema.
 */
class FeaturedThemeSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return FeaturedThemeResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), FeaturedTheme::RELATION_ANIME),
            new AllowedInclude(new ArtistSchema(), FeaturedTheme::RELATION_ARTISTS),
            new AllowedInclude(new EntrySchema(), FeaturedTheme::RELATION_ENTRY),
            new AllowedInclude(new ImageSchema(), FeaturedTheme::RELATION_IMAGES),
            new AllowedInclude(new SongSchema(), FeaturedTheme::RELATION_SONG),
            new AllowedInclude(new ThemeSchema(), FeaturedTheme::RELATION_THEME),
            new AllowedInclude(new UserSchema(), FeaturedTheme::RELATION_USER),
            new AllowedInclude(new VideoSchema(), FeaturedTheme::RELATION_VIDEO),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, FeaturedTheme::ATTRIBUTE_ID),
                new FeaturedThemeStartAtField($this),
                new FeaturedThemeEndAtField($this),
                new FeaturedThemeEntryIdField($this),
                new FeaturedThemeUserIdField($this),
                new FeaturedThemeVideoIdField($this),
            ],
        );
    }
}
