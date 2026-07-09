<?php

declare(strict_types=1);

namespace App\Observers\Wiki;

use App\Models\Wiki\Anime;

class AnimeObserver
{
    /**
     * Handle the Anime "creating" event.
     */
    public function creating(Anime $anime): void
    {
        $anime->year = $anime->start_date?->year;
    }

    /**
     * Handle the Anime "updating" event.
     */
    public function updating(Anime $anime): void
    {
        $anime->year = $anime->start_date?->year;
    }
}
