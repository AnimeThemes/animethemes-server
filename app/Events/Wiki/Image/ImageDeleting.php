<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\BaseEvent;
use App\Models\Wiki\Image;

/**
 * Class ImageDeleting.
 *
 * @extends BaseEvent<Image>
 */
class ImageDeleting extends BaseEvent
{
    public function __construct(Image $image)
    {
        parent::__construct($image);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Image
    {
        return $this->model;
    }
}
