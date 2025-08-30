<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Admin\Feature;

/**
 * @extends AdminDeletedEvent<Feature>
 */
class FeatureDeleted extends AdminDeletedEvent
{
    public function __construct(Feature $feature)
    {
        parent::__construct($feature);
    }

    public function getModel(): Feature
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Feature '**{$this->getModel()->getName()}**' has been deleted.";
    }

    public function shouldSendDiscordMessage(): bool
    {
        return $this->getModel()->isNullScope();
    }
}
