<?php

declare(strict_types=1);

namespace App\Events\Admin\FeaturedTheme;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Admin\FeaturedTheme;

/**
 * @extends AdminDeletedEvent<FeaturedTheme>
 */
class FeaturedThemeDeleted extends AdminDeletedEvent
{
    public function __construct(FeaturedTheme $featuredTheme)
    {
        parent::__construct($featuredTheme);
    }

    public function getModel(): FeaturedTheme
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Featured Theme '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
