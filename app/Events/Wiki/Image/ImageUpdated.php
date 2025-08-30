<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Image;

/**
 * @extends WikiUpdatedEvent<Image>
 */
class ImageUpdated extends WikiUpdatedEvent
{
    public function __construct(Image $image)
    {
        parent::__construct($image);
        $this->initializeEmbedFields($image);
    }

    public function getModel(): Image
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Image '**{$this->getModel()->getName()}**' has been updated.";
    }
}
