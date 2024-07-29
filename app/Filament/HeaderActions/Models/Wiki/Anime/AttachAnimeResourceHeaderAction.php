<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\AttachResourceAction as AttachResourceActionAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;
use App\Models\Wiki\Anime;
use App\Rules\Wiki\Resource\AnimeResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class AttachAnimeResourceHeaderAction.
 */
class AttachAnimeResourceHeaderAction extends AttachResourceHeaderAction
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
            ResourceSite::TWITTER,
            ResourceSite::YOUTUBE,
            ResourceSite::WIKI,
        ]);

        $this->action(fn (Anime $record, array $data) => (new AttachResourceActionAction($record, $data, $this->sites))->handle());
    }

    /**
     * Get the format validation rule.
     *
     * @param  ResourceSite  $site
     * @return ValidationRule
     */
    protected function getFormatRule(ResourceSite $site): ValidationRule
    {
        return new AnimeResourceLinkFormatRule($site);
    }
}
