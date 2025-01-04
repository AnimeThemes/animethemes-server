<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki\Song;

use App\Enums\Models\Wiki\ResourceSite;

/**
 * Trait AttachSongResourceActionTrait.
 */
trait AttachSongResourceActionTrait
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
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::YOUTUBE,
            ResourceSite::APPLE_MUSIC,
            ResourceSite::AMAZON_MUSIC,
        ]);
    }
}
