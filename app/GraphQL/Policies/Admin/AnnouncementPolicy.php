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
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        /** @var Announcement $announcement */
        $announcement = Arr::get($args, $keyName);

        return $announcement->public;
    }
}
