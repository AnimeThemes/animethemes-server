<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Artist;

use App\Actions\Models\Wiki\AttachResourceAction as AttachResourceActionAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;
use App\Models\Wiki\Artist;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class AttachArtistResourceHeaderAction.
 */
class AttachArtistResourceHeaderAction extends AttachResourceHeaderAction
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
            ResourceSite::X,
            ResourceSite::YOUTUBE,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::WIKI,
        ]);

        $this->action(fn (Artist $record, array $data) => (new AttachResourceActionAction($record, $data, $this->sites))->handle());
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
