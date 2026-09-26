<?php

declare(strict_types=1);

namespace App\Events\Wiki\ThemeStaff;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\ThemeStaff;

/**
 * @extends WikiRestoredEvent<ThemeStaff>
 */
class ThemeStaffRestored extends WikiRestoredEvent {}
