<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Song;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;

/**
 * Class AttachSongResourceAction.
 */
class AttachSongResourceAction extends AttachResourceAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('attach-song-resource');

        $this->sites([
            ResourceSite::ANIDB,
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::YOUTUBE,
            ResourceSite::APPLE_MUSIC,
            ResourceSite::AMAZON_MUSIC,
        ]);
    }
}
