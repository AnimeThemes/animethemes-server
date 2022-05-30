<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Models\Auth\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

/**
 * Class JetstreamServiceProvider.
 */
class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Jetstream::useUserModel(User::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }
}
