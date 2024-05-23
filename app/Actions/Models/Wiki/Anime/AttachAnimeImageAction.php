<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime;

use App\Models\Wiki\Image;
use App\Actions\Models\Wiki\AttachImageAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachAnimeImageAction.
 */
class AttachAnimeImageAction extends AttachImageAction
{
    /**
     * Get the relation to the action models.
     *
     * @param  Image  $image
     * @return BelongsToMany
     */
    protected function relation(Image $image): BelongsToMany
    {
        return $image->anime();
    }
}
