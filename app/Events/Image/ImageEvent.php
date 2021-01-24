<?php

namespace App\Events\Image;

use App\Models\Image;

abstract class ImageEvent
{
    /**
     * The image that has fired this event.
     *
     * @var \App\Models\Image
     */
    protected $image;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Image $image
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Get the image that has fired this event.
     *
     * @return \App\Models\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
