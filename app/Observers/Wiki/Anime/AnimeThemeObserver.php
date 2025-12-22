<?php

declare(strict_types=1);

namespace App\Observers\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Support\Str;

class AnimeThemeObserver
{
    /**
     * Handle the AnimeTheme "creating" event.
     */
    public function creating(AnimeTheme $theme): void
    {
        static::setThemeSlug($theme);
    }

    /**
     * Handle the AnimeTheme "updating" event.
     */
    public function updating(AnimeTheme $theme): void
    {
        static::setThemeSlug($theme);
    }

    /**
     * Handle the AnimeTheme "created" event.
     */
    public function created(AnimeTheme $theme): void
    {
        // Update the sequence attribute of the first theme when creating a new sequence theme.
        if ($theme->sequence >= 2) {
            $theme->anime->animethemes()->getQuery()
                ->where(AnimeTheme::ATTRIBUTE_SEQUENCE)
                ->where(AnimeTheme::ATTRIBUTE_TYPE, $theme->type)
                ->update([AnimeTheme::ATTRIBUTE_SEQUENCE => 1]);
        }
    }

    protected static function setThemeSlug(AnimeTheme $theme): void
    {
        $slug = Str::of('');
        $type = $theme->type;

        if (filled($type)) {
            $slug = $slug->append($type->name);
        }

        if ($slug->isNotEmpty() && $type !== ThemeType::IN) {
            $sequence = $theme->sequence;
            $slug = $slug->append(strval(blank($sequence) ? 1 : $sequence));
        }

        if ($slug->isNotEmpty()) {
            $group = $theme->load(AnimeTheme::RELATION_GROUP)->group;

            if ($group instanceof Group) {
                $slug = $slug->append('-'.$group->slug);
            }
        }

        $theme->setAttribute(AnimeTheme::ATTRIBUTE_SLUG, (string) $slug);
      //  dd($theme);
    }
}
