<?php

declare(strict_types=1);

namespace App\Observers\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeObserver
{
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
}
