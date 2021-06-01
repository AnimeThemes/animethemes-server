<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;

/**
 * Class AuthServiceProvider.
 */
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
        'App\Models\Billing\Balance' => 'App\Policies\Billing\BalancePolicy',
        'App\Models\Billing\Transaction' => 'App\Policies\Billing\TransactionPolicy',
        'App\Models\Entry' => 'App\Policies\EntryPolicy',
        'App\Models\Image' => 'App\Policies\ImagePolicy',
        'App\Models\ExternalResource' => 'App\Policies\ExternalResourcePolicy',
        'App\Models\Invitation' => 'App\Policies\InvitationPolicy',
        'App\Models\Series' => 'App\Policies\SeriesPolicy',
        'App\Models\Song' => 'App\Policies\SongPolicy',
        'App\Models\Synonym' => 'App\Policies\SynonymPolicy',
        'App\Models\Team' => 'App\Policies\TeamPolicy',
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

        Password::defaults(function () {
            return Password::min(8)
                ->uncompromised()
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols();
        });

        Gate::define('viewNova', function (User $user) {
            $novaTeam = Team::find(Config::get('nova.team'));

            return $user->isCurrentTeam($novaTeam);
        });
    }
}
