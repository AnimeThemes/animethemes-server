<?php

declare(strict_types=1);

namespace App\Events\Wiki\Entry;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Entry;

/**
 * @extends WikiRestoredEvent<Entry>
 */
class EntryRestored extends WikiRestoredEvent {}
