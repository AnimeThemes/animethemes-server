<?php

declare(strict_types=1);

namespace App\Listeners\Theme;

use App\Events\Theme\ThemeEvent;

/**
 * Class CreateThemeSlug.
 */
class CreateThemeSlug
{
    /**
     * Handle the event.
     *
     * @param ThemeEvent $event
     * @return void
     */
    public function handle(ThemeEvent $event)
    {
        $theme = $event->getTheme();

        $slug = $theme->type->key;
        if (! empty($theme->sequence)) {
            $slug .= $theme->sequence;
        }
        $theme->slug = $slug;
    }
}
