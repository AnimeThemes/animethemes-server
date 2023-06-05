<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Anime;

use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Models\Wiki\AttachResourceAction;
use App\Rules\Wiki\Resource\AnimeResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachAnimeResourceAction.
 */
class AttachAnimeResourceAction extends AttachResourceAction
{
    /**
     * Get the relation to the action models.
     *
     * @param  ExternalResource  $resource
     * @return BelongsToMany
     */
    protected function relation(ExternalResource $resource): BelongsToMany
    {
        return $resource->anime();
    }

    /**
     * Get the format validation rule.
     *
     * @return ValidationRule
     */
    protected function getFormatRule(): ValidationRule
    {
        return new AnimeResourceLinkFormatRule($this->site);
    }
}
