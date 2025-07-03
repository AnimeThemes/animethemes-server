<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Artist;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

/**
 * Class AttachArtistResourceAction.
 */
class AttachArtistResourceAction extends AttachResourceAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('attach-artist-resource');

        $this->sites([
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
            ResourceSite::OFFICIAL_SITE,
            ResourceSite::SPOTIFY,
            ResourceSite::X,
            ResourceSite::YOUTUBE,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::WIKI,
        ]);
    }
}
