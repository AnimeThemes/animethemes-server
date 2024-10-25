<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki\Studio;

use App\Actions\Models\Wiki\AttachResourceAction as AttachResourceActionAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Studio;
use App\Rules\Wiki\Resource\StudioResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;

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

        $this->action(fn (Studio $record, array $data) => (new AttachResourceActionAction($record, $data, $this->sites))->handle());
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
