<?php

declare(strict_types=1);

namespace App\Events\Admin\FeaturedTheme;

use App\Events\Base\Admin\AdminCreatedEvent;
use App\Models\Admin\FeaturedTheme;

/**
 * @extends AdminCreatedEvent<FeaturedTheme>
 */
class FeaturedThemeCreated extends AdminCreatedEvent {}
