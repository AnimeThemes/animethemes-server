<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\Admin;

use App\GraphQL\Policies\BasePolicy;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Illuminate\Support\Arr;

class AnnouncementPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  array|null  $injected
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        /** @var Announcement $announcement */
        $announcement = Arr::get($injected, $keyName);

        return $announcement->public;
    }
}
