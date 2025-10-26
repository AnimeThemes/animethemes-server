<?php

declare(strict_types=1);

namespace App\Events\Admin\FeaturedTheme;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Admin\FeaturedTheme;

/**
 * @extends AdminUpdatedEvent<FeaturedTheme>
 */
class FeaturedThemeUpdated extends AdminUpdatedEvent
{
    public function __construct(FeaturedTheme $featuredTheme)
    {
        parent::__construct($featuredTheme);
        $this->initializeEmbedFields($featuredTheme);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Featured Theme '**{$this->getModel()->getName()}**' has been updated.";
    }
}
