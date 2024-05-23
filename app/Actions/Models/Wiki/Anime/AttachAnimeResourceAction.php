<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime;

use App\Models\Wiki\ExternalResource;
use App\Actions\Models\Wiki\AttachResourceAction;
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
}
