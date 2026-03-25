<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Artist;

/**
 * @extends WikiCreatedEvent<Artist>
 */
class ArtistCreated extends WikiCreatedEvent {}
