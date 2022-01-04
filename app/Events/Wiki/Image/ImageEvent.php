<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Models\Wiki\Image;

/**
 * Class ImageEvent.
 */
abstract class ImageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Image  $image
     * @return void
     */
    public function __construct(protected Image $image)
    {
    }

    /**
     * Get the image that has fired this event.
     *
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }
}
