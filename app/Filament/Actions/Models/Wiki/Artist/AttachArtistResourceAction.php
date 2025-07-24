<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Artist;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

class AttachArtistResourceAction extends AttachResourceAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'attach-artist-resource';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

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
