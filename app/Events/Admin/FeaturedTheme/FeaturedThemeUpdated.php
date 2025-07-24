<?php

declare(strict_types=1);

namespace App\Events\Admin\FeaturedTheme;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Admin\FeaturedTheme;

/**
 * Class FeaturedThemeUpdated.
 *
 * @extends AdminUpdatedEvent<FeaturedTheme>
 */
class FeaturedThemeUpdated extends AdminUpdatedEvent
{
    public function __construct(FeaturedTheme $featuredTheme)
    {
        parent::__construct($featuredTheme);
        $this->initializeEmbedFields($featuredTheme);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): FeaturedTheme
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Featured Theme '**{$this->getModel()->getName()}**' has been updated.";
    }
}
