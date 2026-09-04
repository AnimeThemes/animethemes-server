<?php

declare(strict_types=1);

namespace App\Observers\Wiki;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Group;
use App\Models\Wiki\Theme;
use Illuminate\Support\Str;

class ThemeObserver
{
    /**
     * Handle the AnimeTheme "creating" event.
     */
    public function creating(Theme $theme): void
    {
        static::setThemeSlug($theme);
    }

    /**
     * Handle the AnimeTheme "updating" event.
     */
    public function updating(Theme $theme): void
    {
        static::setThemeSlug($theme);
    }

    /**
     * Handle the AnimeTheme "created" event.
     */
    public function created(Theme $theme): void
    {
        // Update the sequence attribute of the first theme when creating a new sequence theme.
        if ($theme->sequence >= 2) {
            $theme->anime->animethemes()->getQuery()
                ->where(Theme::ATTRIBUTE_SEQUENCE)
                ->where(Theme::ATTRIBUTE_TYPE, $theme->type)
                ->update([Theme::ATTRIBUTE_SEQUENCE => 1]);
        }
    }

    protected static function setThemeSlug(Theme $theme): void
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
            $group = $theme->load(Theme::RELATION_GROUP)->group;

            if ($group instanceof Group) {
                $slug = $slug->append('-'.$group->slug);
            }
        }

        $theme->setAttribute(Theme::ATTRIBUTE_SLUG, (string) $slug);
    }
}
