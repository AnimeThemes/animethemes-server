<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pipes\Wiki\Anime\BackfillAnidbResource;
use App\Pipes\Wiki\Anime\BackfillAnilistResource;
use App\Pipes\Wiki\Anime\BackfillAnimeStudios;
use App\Pipes\Wiki\Anime\BackfillKitsuResource;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class BackfillAnimeAction.
 */
class BackfillAnimeAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const BACKFILL_ANIDB_RESOURCE = 'backfill_anidb_resource';
    final public const BACKFILL_ANILIST_RESOURCE = 'backfill_anilist_resource';
    final public const BACKFILL_ANIME_STUDIOS = 'backfill_anime_studios';
    final public const BACKFILL_KITSU_RESOURCE = 'backfill_kitsu_resource';

    /**
     * Create a new action instance.
     *
     * @param  User  $user
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
     */
    public function handle(ActionFields $fields, Collection $models): mixed
    {
        $anime = $models->first();

        if ($anime->resources()->doesntExist()) {
            return $this->markAsFailed($anime, 'At least one Resource is required to backfill Anime');
        }

        $pipes = $this->getPipes($fields, $anime);

        $pipeline = new Pipeline(Container::getInstance());

        try {
            return $pipeline->send($this->user)
                ->through($pipes)
                ->then(fn () => $this->markAsFinished($anime));
        } catch (Exception $e) {
            return $this->markAsFailed($anime, $e);
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        $anime = $request->resourceId !== null
            ? $request->findModel()
            : null;

        return [
            Boolean::make(__('nova.backfill_kitsu_resource'), self::BACKFILL_KITSU_RESOURCE)
                ->help(__('nova.backfill_kitsu_resource_help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)->doesntExist()),

            Boolean::make(__('nova.backfill_anilist_resource'), self::BACKFILL_ANILIST_RESOURCE)
                ->help(__('nova.backfill_anilist_resource_help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST)->doesntExist()),

            Boolean::make(__('nova.backfill_anidb_resource'), self::BACKFILL_ANIDB_RESOURCE)
                ->help(__('nova.backfill_anidb_resource_help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)->doesntExist()),

            Boolean::make(__('nova.backfill_anime_studios'), self::BACKFILL_ANIME_STUDIOS)
                ->help(__('nova.backfill_anime_studios_help'))
                ->default(fn () => $anime instanceof Anime && $anime->studios()->doesntExist()),
        ];
    }

    /**
     * Get the selected pipes for backfilling anime.
     *
     * @param  ActionFields  $fields
     * @param  Anime  $anime
     * @return array
     */
    protected function getPipes(ActionFields $fields, Anime $anime): array
    {
        $pipes = [];

        if (Arr::get($fields, self::BACKFILL_KITSU_RESOURCE) === true) {
            $pipes[] = new BackfillKitsuResource($anime);
        }

        if (Arr::get($fields, self::BACKFILL_ANILIST_RESOURCE) === true) {
            $pipes[] = new BackfillAnilistResource($anime);
        }

        if (Arr::get($fields, self::BACKFILL_ANIDB_RESOURCE) === true) {
            $pipes[] = new BackfillAnidbResource($anime);
        }

        if (Arr::get($fields, self::BACKFILL_ANIME_STUDIOS) === true) {
            $pipes[] = new BackfillAnimeStudios($anime);
        }

        return $pipes;
    }
}
