<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

class AttachAnimeResourceAction extends AttachResourceAction
{
    public static function getDefaultName(): ?string
    {
        return 'attach-anime-resource';
    }

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
