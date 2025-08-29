<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * @extends BaseEvent<Image>
 */
class ImageForceDeleting extends BaseEvent implements RemoveFromStorageEvent
{
    public function __construct(Image $image)
    {
        parent::__construct($image);
    }

    public function getModel(): Image
    {
        return $this->model;
    }

    /**
     * Remove the image from the bucket.
     */
    public function removeFromStorage(): void
    {
        Storage::disk(Config::get('image.disk'))->delete($this->getModel()->path);
    }
}
