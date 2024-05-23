<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\Anime\AttachAnimeResourceAction as AttachAnimeResourceActionAction;
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

        $this->action(fn (Anime $record, array $data) => (new AttachAnimeResourceActionAction($this->sites))->handle($record, $data));
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
