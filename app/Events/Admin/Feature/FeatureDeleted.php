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
    public function shouldSendDiscordMessage(): bool
    {
        return $this->getModel()->isNullScope();
    }
}
