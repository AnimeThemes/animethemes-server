<?php

declare(strict_types=1);

namespace App\Events\Wiki\Group;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Group;

/**
 * @extends WikiCreatedEvent<Group>
 */
class GroupCreated extends WikiCreatedEvent {}
