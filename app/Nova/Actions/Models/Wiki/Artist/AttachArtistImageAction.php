<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Artist;

use App\Models\Wiki\Image;
use App\Nova\Actions\Models\Wiki\AttachImageAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachArtistImageAction.
 */
class AttachArtistImageAction extends AttachImageAction
{
    /**
     * Get the relation to the action models.
     *
     * @param  Image  $image
     * @return BelongsToMany
     */
    protected function relation(Image $image): BelongsToMany
    {
        return $image->artists();
    }
}
