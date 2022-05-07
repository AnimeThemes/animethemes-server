<?php

declare(strict_types=1);

namespace App\Listeners\Wiki\Anime\Theme;

use App\Events\Wiki\Anime\Theme\ThemeCreating;

/**
 * Class CreateThemeSlug.
 */
class CreateThemeSlug
{
    /**
     * Handle the event.
     *
     * @param  ThemeCreating  $event
     * @return void
     */
    public function handle(ThemeCreating $event): void
    {
        $theme = $event->getModel();

        $slug = $theme->type?->key;
        if (! empty($theme->sequence)) {
            $slug .= $theme->sequence;
        }
        $theme->slug = $slug;
    }
}
