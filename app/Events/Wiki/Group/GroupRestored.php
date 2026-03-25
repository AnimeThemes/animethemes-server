<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Group;

/**
 * @extends WikiRestoredEvent<Group>
 */
class GroupRestored extends WikiRestoredEvent {}
