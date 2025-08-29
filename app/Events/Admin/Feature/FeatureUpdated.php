<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Admin\Feature;

/**
 * @extends AdminUpdatedEvent<Feature>
 */
class FeatureUpdated extends AdminUpdatedEvent
{
    public function __construct(Feature $feature)
    {
        parent::__construct($feature);
        $this->initializeEmbedFields($feature);
    }

    public function getModel(): Feature
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Feature '**{$this->getModel()->getName()}**' has been updated.";
    }

    public function shouldSendDiscordMessage(): bool
    {
        return $this->getModel()->isNullScope();
    }
}
