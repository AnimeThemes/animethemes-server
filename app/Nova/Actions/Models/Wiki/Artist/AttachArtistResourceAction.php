<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Artist;

use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Models\Wiki\AttachResourceAction;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachArtistResourceAction.
 */
class AttachArtistResourceAction extends AttachResourceAction
{
    /**
     * Get the relation to the action models.
     *
     * @param  ExternalResource  $resource
     * @return BelongsToMany
     */
    protected function relation(ExternalResource $resource): BelongsToMany
    {
        return $resource->artists();
    }

    /**
     * Get the format validation rule.
     *
     * @return ValidationRule
     */
    protected function getFormatRule(): ValidationRule
    {
        return new ArtistResourceLinkFormatRule($this->site);
    }
}
