<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\BaseEvent;
use App\Models\Wiki\Image;

/**
 * @extends BaseEvent<Image>
 */
class ImageDeleting extends BaseEvent
{
    public function __construct(Image $image)
    {
        parent::__construct($image);
    }

    public function getModel(): Image
    {
        return $this->model;
    }
}
