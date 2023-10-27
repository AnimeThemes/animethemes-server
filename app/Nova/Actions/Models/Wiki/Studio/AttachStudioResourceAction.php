<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Models\Wiki\AttachResourceAction;
use App\Rules\Wiki\Resource\StudioResourceLinkFormatRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachStudioResourceAction.
 */
class AttachStudioResourceAction extends AttachResourceAction
{
    /**
     * Get the relation to the action models.
     *
     * @param  ExternalResource  $resource
     * @return BelongsToMany
     */
    protected function relation(ExternalResource $resource): BelongsToMany
    {
        return $resource->studios();
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
