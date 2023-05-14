<?php

declare(strict_types=1);

namespace App\Events\Admin\FeaturedTheme;

use App\Events\Base\Admin\AdminRestoredEvent;
use App\Models\Admin\FeaturedTheme;

/**
 * Class FeaturedThemeRestored.
 *
 * @extends AdminRestoredEvent<FeaturedTheme>
 */
class FeaturedThemeRestored extends AdminRestoredEvent
{
    /**
     * Create a new event instance.
     *
     * @param  FeaturedTheme  $featuredTheme
     */
    public function __construct(FeaturedTheme $featuredTheme)
    {
        parent::__construct($featuredTheme);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return FeaturedTheme
     */
    public function getModel(): FeaturedTheme
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Featured Theme '**{$this->getModel()->getName()}**' has been restored.";
    }
}
