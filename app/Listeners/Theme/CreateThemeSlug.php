<?php

namespace App\Listeners\Theme;

use App\Events\Theme\ThemeEvent;

class CreateThemeSlug
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Theme\ThemeEvent  $event
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
