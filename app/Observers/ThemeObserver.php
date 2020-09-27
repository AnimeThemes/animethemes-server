<?php

namespace App\Observers;

use App\Models\Theme;

class ThemeObserver
{
    /**
     * Handle the theme "creating" event.
     *
     * @param  \App\Models\Theme  $theme
     * @return void
     */
    public function creating(Theme $theme)
    {
        $slug = $theme->type->key;
        if (!empty($theme->sequence)) {
            $slug .= $theme->sequence;
        }
        $theme->slug = $slug;
    }

    /**
     * Handle the theme "created" event.
     *
     * @param  \App\Models\Theme  $theme
     * @return void
     */
    public function created(Theme $theme)
    {
        $this->updateRelatedScoutIndices($theme);
    }

    /**
     * Handle the theme "updating" event.
     *
     * @param  \App\Models\Theme  $theme
     * @return void
     */
    public function updating(Theme $theme)
    {
        $slug = $theme->type->key;
        if (!empty($theme->sequence)) {
            $slug .= $theme->sequence;
        }
        $theme->slug = $slug;
    }

    /**
     * Handle the theme "updated" event.
     *
     * @param  \App\Models\Theme  $theme
     * @return void
     */
    public function updated(Theme $theme)
    {
        $this->updateRelatedScoutIndices($theme);
    }

    /**
     * Handle the theme "deleted" event.
     *
     * @param  \App\Models\Theme  $theme
     * @return void
     */
    public function deleted(Theme $theme)
    {
        $this->updateRelatedScoutIndices($theme);
    }

    /**
     * Handle updating of related index documents
     *
     * @param  \App\Models\Theme  $theme
     * @return void
     */
    private function updateRelatedScoutIndices(Theme $theme) : void {
        $theme->entries->each(function ($entry) {
            $entry->searchable();
            $entry->videos->searchable();
        });
    }
}
