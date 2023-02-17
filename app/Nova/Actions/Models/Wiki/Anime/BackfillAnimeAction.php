<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Anime;

use App\Actions\Models\BackfillAction;
use App\Actions\Models\Wiki\Anime\Image\BackfillLargeCoverImageAction;
use App\Actions\Models\Wiki\Anime\Image\BackfillSmallCoverImageAction;
use App\Actions\Models\Wiki\Anime\Resource\BackfillAnidbResourceAction;
use App\Actions\Models\Wiki\Anime\Resource\BackfillAnilistResourceAction;
use App\Actions\Models\Wiki\Anime\Resource\BackfillAnnResourceAction;
use App\Actions\Models\Wiki\Anime\Resource\BackfillKitsuResourceAction;
use App\Actions\Models\Wiki\Anime\Resource\BackfillMalResourceAction;
use App\Actions\Models\Wiki\Anime\Studio\BackfillAnimeStudiosAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Nova\Resources\Wiki\Anime as AnimeResource;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Notifications\NovaNotification;

/**
 * Class BackfillAnimeAction.
 */
class BackfillAnimeAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const BACKFILL_ANIDB_RESOURCE = 'backfill_anidb_resource';
    final public const BACKFILL_ANILIST_RESOURCE = 'backfill_anilist_resource';
    final public const BACKFILL_ANN_RESOURCE = 'backfill_ann_resource';
    final public const BACKFILL_KITSU_RESOURCE = 'backfill_kitsu_resource';
    final public const BACKFILL_LARGE_COVER = 'backfill_large_cover';
    final public const BACKFILL_MAL_RESOURCE = 'backfill_mal_resource';
    final public const BACKFILL_SMALL_COVER = 'backfill_small_cover';
    final public const BACKFILL_STUDIOS = 'backfill_studios';

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
        return __('nova.actions.anime.backfill.name');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Anime>  $models
     * @return Collection
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {
        $uriKey = AnimeResource::uriKey();

        foreach ($models as $anime) {
            if ($anime->resources()->doesntExist()) {
                $this->markAsFailed($anime, __('nova.actions.anime.backfill.message.resource_required_failure'));
                continue;
            }

            $actions = $this->getActions($fields, $anime);

            try {
                foreach ($actions as $action) {
                    $result = $action->handle();
                    if ($result->hasFailed()) {
                        $this->user->notify(
                            NovaNotification::make()
                                ->icon('flag')
                                ->message($result->getMessage())
                                ->type(NovaNotification::WARNING_TYPE)
                                ->url("/resources/$uriKey/{$anime->getKey()}")
                        );
                    }
                }
            } catch (Exception $e) {
                $this->markAsFailed($anime, $e);
            } finally {
                // Try not to upset third-party APIs
                sleep(rand(3, 5));
            }
        }

        return $models;
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
        $anime = $request->findModelQuery()->first();

        return [
            Heading::make(__('nova.actions.anime.backfill.fields.resources.name')),

            Boolean::make(__('nova.actions.anime.backfill.fields.resources.kitsu.name'), self::BACKFILL_KITSU_RESOURCE)
                ->help(__('nova.actions.anime.backfill.fields.resources.kitsu.help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)->doesntExist()),

            Boolean::make(__('nova.actions.anime.backfill.fields.resources.anilist.name'), self::BACKFILL_ANILIST_RESOURCE)
                ->help(__('nova.actions.anime.backfill.fields.resources.anilist.help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST)->doesntExist()),

            Boolean::make(__('nova.actions.anime.backfill.fields.resources.mal.name'), self::BACKFILL_MAL_RESOURCE)
                ->help(__('nova.actions.anime.backfill.fields.resources.mal.help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL)->doesntExist()),

            Boolean::make(__('nova.actions.anime.backfill.fields.resources.anidb.name'), self::BACKFILL_ANIDB_RESOURCE)
                ->help(__('nova.actions.anime.backfill.fields.resources.anidb.help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)->doesntExist()),

            Boolean::make(__('nova.actions.anime.backfill.fields.resources.ann.name'), self::BACKFILL_ANN_RESOURCE)
                ->help(__('nova.actions.anime.backfill.fields.resources.ann.help'))
                ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANN)->doesntExist()),

            Heading::make(__('nova.actions.anime.backfill.fields.images.name')),

            Boolean::make(__('nova.actions.anime.backfill.fields.images.large_cover.name'), self::BACKFILL_LARGE_COVER)
                ->help(__('nova.actions.anime.backfill.fields.images.large_cover.help'))
                ->default(fn () => $anime instanceof Anime && $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->doesntExist()),

            Boolean::make(__('nova.actions.anime.backfill.fields.images.small_cover.name'), self::BACKFILL_SMALL_COVER)
                ->help(__('nova.actions.anime.backfill.fields.images.small_cover.help'))
                ->default(fn () => $anime instanceof Anime && $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_SMALL)->doesntExist()),

            Heading::make(__('nova.actions.anime.backfill.fields.studios.name')),

            Boolean::make(__('nova.actions.anime.backfill.fields.studios.anime.name'), self::BACKFILL_STUDIOS)
                ->help(__('nova.actions.anime.backfill.fields.studios.anime.help'))
                ->default(fn () => $anime instanceof Anime && $anime->studios()->doesntExist()),
        ];
    }

    /**
     * Get the selected actions for backfilling anime.
     *
     * @param  ActionFields  $fields
     * @param  Anime  $anime
     * @return BackfillAction[]
     */
    protected function getActions(ActionFields $fields, Anime $anime): array
    {
        $actions = [];

        foreach ($this->getActionMapping($anime) as $field => $action) {
            if (Arr::get($fields, $field) === true) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    /**
     * Get the mapping of actions to their form fields.
     *
     * @param  Anime  $anime
     * @return array<string, BackfillAction>
     */
    protected function getActionMapping(Anime $anime): array
    {
        return [
            self::BACKFILL_KITSU_RESOURCE => new BackfillKitsuResourceAction($anime),
            self::BACKFILL_ANILIST_RESOURCE => new BackfillAnilistResourceAction($anime),
            self::BACKFILL_MAL_RESOURCE => new BackfillMalResourceAction($anime),
            self::BACKFILL_ANIDB_RESOURCE => new BackfillAnidbResourceAction($anime),
            self::BACKFILL_ANN_RESOURCE => new BackfillAnnResourceAction($anime),
            self::BACKFILL_LARGE_COVER => new BackfillLargeCoverImageAction($anime),
            self::BACKFILL_SMALL_COVER => new BackfillSmallCoverImageAction($anime),
            self::BACKFILL_STUDIOS => new BackfillAnimeStudiosAction($anime),
        ];
    }
}
