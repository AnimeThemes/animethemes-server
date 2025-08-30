<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Image as ImageFilament;
use App\Models\Wiki\Image;

/**
 * @extends WikiDeletedEvent<Image>
 */
class ImageDeleted extends WikiDeletedEvent
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
        return "Image '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Image '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return ImageFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
