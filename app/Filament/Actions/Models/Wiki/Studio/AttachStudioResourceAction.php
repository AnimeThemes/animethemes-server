<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Studio;

use App\Actions\Models\Wiki\Studio\AttachStudioResourceAction as AttachStudioResourceActionAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;
use App\Models\Wiki\Studio;
use App\Rules\Wiki\Resource\StudioResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class AttachStudioResourceAction.
 */
class AttachStudioResourceAction extends AttachResourceAction
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

        $this->action(fn (Studio $record, array $data) => (new AttachStudioResourceActionAction($this->sites))->handle($record, $data));
    }

    /**
     * Get the format validation rule.
     *
     * @param  ResourceSite  $site
     * @return ValidationRule
     */
    protected function getFormatRule(ResourceSite $site): ValidationRule
    {
        return new StudioResourceLinkFormatRule($site);
    }
}
