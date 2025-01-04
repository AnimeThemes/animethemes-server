<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;

/**
 * Trait AttachAnimeResourceActionTrait.
 */
trait AttachAnimeResourceActionTrait
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sites([
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::KITSU,
            ResourceSite::LIVECHART,
            ResourceSite::MAL,
            ResourceSite::OFFICIAL_SITE,
            ResourceSite::X,
            ResourceSite::YOUTUBE,
            ResourceSite::WIKI,
        ]);
    }
}
