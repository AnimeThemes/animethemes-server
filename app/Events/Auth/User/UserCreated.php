<?php

declare(strict_types=1);

namespace App\Events\Auth\User;

use App\Events\Base\Admin\AdminCreatedEvent;
use App\Models\Auth\User;

/**
 * @extends AdminCreatedEvent<User>
 */
class UserCreated extends AdminCreatedEvent {}
