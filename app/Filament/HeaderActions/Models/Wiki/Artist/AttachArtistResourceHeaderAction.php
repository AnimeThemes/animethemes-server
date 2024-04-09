<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Artist;

use App\Actions\Models\Wiki\Artist\AttachArtistResourceAction as AttachArtistResourceActionAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Models\Wiki\AttachResourceHeaderAction;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

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

        $this->action(fn (Model $record, array $data) => (new AttachArtistResourceActionAction($this->sites))->handle($record, $data));
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
