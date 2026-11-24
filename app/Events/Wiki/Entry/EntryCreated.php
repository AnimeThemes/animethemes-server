<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Entry;

/**
 * @extends WikiCreatedEvent<Entry>
 */
class EntryCreated extends WikiCreatedEvent {}
