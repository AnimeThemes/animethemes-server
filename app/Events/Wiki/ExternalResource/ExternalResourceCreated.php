<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\ExternalResource;

/**
 * @extends WikiCreatedEvent<ExternalResource>
 */
class ExternalResourceCreated extends WikiCreatedEvent {}
