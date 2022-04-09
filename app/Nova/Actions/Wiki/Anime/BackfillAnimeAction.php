<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pipes\Wiki\Anime\MyAnimeListAnimeStudios;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class BackfillAnimeAction.
 */
class BackfillAnimeAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * Create a new action instance.
     *
     * @param User $user
     */
    public function __construct(protected User $user)
    {
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.backfill_anime');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Anime>  $models
     * @return mixed
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $anime = $models->first();

        $malResource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
        if (! $malResource instanceof ExternalResource) {
            return $this->markAsFailed($anime, 'MAL Resource required to backfill Anime');
        }
        if ($malResource->external_id === null) {
            return $this->markAsFailed($anime, 'MAL Resource External Id required to backfill Anime');
        }

        $pipes = [
            new MyAnimeListAnimeStudios($anime, $malResource),
        ];

        $pipeline = new Pipeline(Container::getInstance());

        try {
            return $pipeline->send($this->user)
                ->through($pipes)
                ->then(fn () => $this->markAsFinished($anime));
        } catch (Exception $e) {
            return $this->markAsFailed($anime, $e);
        }
    }
}
