<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Image;

/**
 * @extends WikiRestoredEvent<Image>
 */
class ImageRestored extends WikiRestoredEvent
{
    public function __construct(Image $image)
    {
        parent::__construct($image);
    }

    public function getModel(): Image
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Image '**{$this->getModel()->getName()}**' has been restored.";
    }
}
