<?php

declare(strict_types=1);

namespace App\Services\Nova;

use App\Models\Auth\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

/**
 * Class NovaQueries.
 */
class NovaQueries
{
    /**
     * Get nova users with admin privileges.
     *
     * @return Collection<int, User>
     */
    public static function admins(): Collection
    {
        return User::query()->whereIn('id', function (Builder $query) {
            $query->select('user_id')
                ->from('team_user')
                ->whereColumn('team_user.user_id', 'users.id')
                ->where('team_user.team_id', Config::get('teams.nova'))
                ->where('team_user.role', 'admin');
        })
        ->orWhere('id', function (Builder $query) {
            $query->select('user_id')
                ->from('teams')
                ->whereColumn('teams.user_id', 'users.id')
                ->where('teams.id', Config::get('teams.nova'));
        })
        ->get();
    }
}
