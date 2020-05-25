<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
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
        'App\Models\Entry' => 'App\Policies\EntryPolicy',
        'App\Models\ExternalResource' => 'App\Policies\ExternalResourcePolicy',
        'App\Models\Invitation' => 'App\Policies\InvitationPolicy',
        'App\Models\Series' => 'App\Policies\SeriesPolicy',
        'App\Models\Song' => 'App\Policies\SongPolicy',
        'App\Models\Synonym' => 'App\Policies\SynonymPolicy',
        'App\Models\Theme' => 'App\Policies\ThemePolicy',
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

        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}
