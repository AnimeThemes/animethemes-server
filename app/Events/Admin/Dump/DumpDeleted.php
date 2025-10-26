<?php

declare(strict_types=1);

namespace App\Events\Admin\Dump;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Admin\Dump;

/**
 * @extends AdminDeletedEvent<Dump>
 */
class DumpDeleted extends AdminDeletedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Dump '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
