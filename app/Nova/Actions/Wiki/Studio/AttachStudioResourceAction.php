<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Studio;

use App\Models\Wiki\ExternalResource;
use App\Nova\Actions\Wiki\AttachResourceAction;
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
}
