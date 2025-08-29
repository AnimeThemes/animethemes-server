<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Image;

/**
 * @extends WikiCreatedEvent<Image>
 */
class ImageCreated extends WikiCreatedEvent
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

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Image '**{$this->getModel()->getName()}**' has been created.";
    }
}
