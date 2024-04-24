<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Image;

use App\Models\Wiki\Image;
use App\Nova\Actions\Models\Wiki\AttachImageAction as AttachImageActionAction;

/**
 * Class AttachImageAction.
 */
class AttachImageAction extends AttachImageActionAction
{
    /**
     * Get the relation to the action models.
     *
     * @param  Image  $image
     * @return Image
     */
    protected function relation(Image $image): Image
    {
        return $image;
    }
}
