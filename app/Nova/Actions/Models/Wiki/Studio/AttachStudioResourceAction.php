<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Studio;

use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Models\Wiki\AttachResourceAction;
use App\Rules\Wiki\Resource\StudioResourceLinkFormatRule;
use Illuminate\Contracts\Validation\Rule;
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
     * @return Rule
     */
    protected function getFormatRule(): Rule
    {
        return new StudioResourceLinkFormatRule($this->site);
    }
}
