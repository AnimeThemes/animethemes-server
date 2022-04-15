<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime;

use App\Contracts\Pipes\Pipe;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime as AnimeResource;
use Illuminate\Support\Str;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\Nova;

/**
 * BackfillAnimePipe.
 */
abstract class BackfillAnimePipe implements Pipe
{
    /**
     * Create new pipe instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(protected Anime $anime)
    {
    }

    /**
     * Send notification for user to review anime without studios.
     *
     * @param  User  $user
     * @param  string  $message
     * @return void
     */
    protected function sendNotification(User $user, string $message): void
    {
        // Nova requires a relative route without the base path
        $url = route(
            'nova.pages.detail',
            ['resource' => AnimeResource::uriKey(), 'resourceId' => $this->anime->getKey()],
            false
        );
        $url = Str::remove(Nova::path(), $url);

        $user->notify(
            NovaNotification::make()
                ->icon('flag')
                ->message($message)
                ->type(NovaNotification::WARNING_TYPE)
                ->url($url)
        );
    }
}
