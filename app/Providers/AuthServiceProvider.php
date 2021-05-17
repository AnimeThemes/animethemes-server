<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Anime' => 'App\Policies\AnimePolicy',
        'App\Models\Announcement' => 'App\Policies\AnnouncementPolicy',
        'App\Models\Artist' => 'App\Policies\ArtistPolicy',
        'App\Models\Balance' => 'App\Policies\BalancePolicy',
        'App\Models\Entry' => 'App\Policies\EntryPolicy',
        'App\Models\Image' => 'App\Policies\ImagePolicy',
        'App\Models\ExternalResource' => 'App\Policies\ExternalResourcePolicy',
        'App\Models\Invitation' => 'App\Policies\InvitationPolicy',
        'App\Models\Series' => 'App\Policies\SeriesPolicy',
        'App\Models\Song' => 'App\Policies\SongPolicy',
        'App\Models\Synonym' => 'App\Policies\SynonymPolicy',
        'App\Models\Team' => 'App\Policies\TeamPolicy',
        'App\Models\Theme' => 'App\Policies\ThemePolicy',
        'App\Models\Transaction' => 'App\Policies\TransactionPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Video' => 'App\Policies\VideoPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('viewNova', function (User $user) {
            $nova_team = Team::find(Config::get('nova.team'));

            return $user->isCurrentTeam($nova_team);
        });
    }
}
