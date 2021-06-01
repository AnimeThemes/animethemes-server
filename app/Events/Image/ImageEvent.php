<?php

declare(strict_types=1);

namespace App\Events\Image;

use App\Models\Image;

/**
 * Class ImageEvent.
 */
abstract class ImageEvent
{
    /**
     * The image that has fired this event.
     *
     * @var Image
     */
    protected Image $image;

    /**
     * Create a new event instance.
     *
     * @param Image $image
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
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
