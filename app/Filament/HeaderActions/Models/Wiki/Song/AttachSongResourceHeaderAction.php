<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Song;

use App\Actions\Models\Wiki\AttachResourceAction as AttachResourceActionAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;
use App\Models\Wiki\Song;
use App\Rules\Wiki\Resource\SongResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class AttachSongResourceHeaderAction.
 */
class AttachSongResourceHeaderAction extends AttachResourceHeaderAction
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

        $this->action(fn (Song $record, array $data) => (new AttachResourceActionAction($record, $data, $this->sites))->handle());
    }

    /**
     * Get the format validation rule.
     *
     * @param  ResourceSite  $site
     * @return ValidationRule
     */
    protected function getFormatRule(ResourceSite $site): ValidationRule
    {
        return new SongResourceLinkFormatRule($site);
    }
}
