<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Artist;

use App\Actions\Models\Wiki\Artist\AttachArtistResourceAction as AttachArtistResourceActionAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;
use App\Models\Wiki\Artist;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;

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

        $this->sites([
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
            ResourceSite::OFFICIAL_SITE,
            ResourceSite::SPOTIFY,
            ResourceSite::TWITTER,
            ResourceSite::YOUTUBE,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::WIKI,
        ]);

        $this->action(fn (Artist $record, array $data) => (new AttachArtistResourceActionAction($this->sites))->handle($record, $data));
    }

    /**
     * Get the format validation rule.
     *
     * @param  ResourceSite  $site
     * @return ValidationRule
     */
    protected function getFormatRule(ResourceSite $site): ValidationRule
    {
        return new ArtistResourceLinkFormatRule($site);
    }
}
