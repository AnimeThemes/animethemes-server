<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImageForceDeleting.
 *
 * @extends BaseEvent<Image>
 */
class ImageForceDeleting extends BaseEvent implements RemoveFromStorageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Image  $image
     */
    public function __construct(Image $image)
    {
        parent::__construct($image);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Image
     */
    public function getModel(): Image
    {
        return $this->model;
    }

    /**
     * Remove the image from the bucket.
     *
     * @return void
     */
    public function removeFromStorage(): void
    {
        Storage::disk(Config::get('image.disk'))->delete($this->getModel()->path);
    }
}
