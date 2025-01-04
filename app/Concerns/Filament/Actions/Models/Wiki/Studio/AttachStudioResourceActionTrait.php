<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki\Studio;

use App\Enums\Models\Wiki\ResourceSite;

/**
 * Trait AttachStudioResourceActionTrait.
 */
trait AttachStudioResourceActionTrait
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
            ResourceSite::MAL,
        ]);
    }
}
