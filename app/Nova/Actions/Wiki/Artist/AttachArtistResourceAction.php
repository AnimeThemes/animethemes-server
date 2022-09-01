<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Artist;

use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Wiki\AttachResourceAction;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Contracts\Validation\Rule;
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
     * @return Rule
     */
    protected function getFormatRule(): Rule
    {
        return new ArtistResourceLinkFormatRule($this->site);
    }
}
