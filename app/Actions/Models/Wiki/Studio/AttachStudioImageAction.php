<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Studio;

use App\Models\Wiki\Image;
use App\Actions\Models\Wiki\AttachImageAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachStudioImageAction.
 */
class AttachStudioImageAction extends AttachImageAction
{
    /**
     * Get the relation to the action models.
     *
     * @param  Image  $image
     * @return BelongsToMany
     */
    protected function relation(Image $image): BelongsToMany
    {
        return $image->studios();
    }
}
