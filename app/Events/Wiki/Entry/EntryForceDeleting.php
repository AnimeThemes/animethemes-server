<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Events\BaseEvent;
use App\Models\Wiki\Entry;

/**
 * @extends BaseEvent<Entry>
 */
class EntryForceDeleting extends BaseEvent {}
