<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Entry;

/**
 * @extends WikiUpdatedEvent<Entry>
 */
class EntryUpdated extends WikiUpdatedEvent
{
    public function __construct(Entry $entry)
    {
        parent::__construct($entry);
        $this->initializeEmbedFields($entry);
    }
}
