<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\BackfillAnimeAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\BaseHeaderAction;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Sleep;

/**
 * Class BackfillAnimeHeaderAction.
 */
class BackfillAnimeHeaderAction extends BaseHeaderAction implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const RESOURCES = BackfillAnimeAction::RESOURCES;
    final public const IMAGES = BackfillAnimeAction::IMAGES;
    final public const STUDIOS = BackfillAnimeAction::STUDIOS;
    final public const SYNONYMS = BackfillAnimeAction::SYNONYMS;

    final public const BACKFILL_ANIDB_RESOURCE = 'backfill_anidb_resource';
    final public const BACKFILL_ANILIST_RESOURCE = 'backfill_anilist_resource';
    final public const BACKFILL_ANIME_PLANET_RESOURCE = 'backfill_anime_planet_resource';
    final public const BACKFILL_ANN_RESOURCE = 'backfill_ann_resource';
    final public const BACKFILL_KITSU_RESOURCE = 'backfill_kitsu_resource';
    final public const BACKFILL_OTHER_RESOURCES = 'backfill_other_resources';
    final public const BACKFILL_LARGE_COVER = 'backfill_large_cover';
    final public const BACKFILL_MAL_RESOURCE = 'backfill_mal_resource';
    final public const BACKFILL_SMALL_COVER = 'backfill_small_cover';
    final public const BACKFILL_STUDIOS = 'backfill_studios';
    final public const BACKFILL_SYNONYMS = 'backfill_synonyms';

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.anime.backfill.name'));
        $this->icon('heroicon-o-bars-4');

        $this->authorize('create', Anime::class);

        $this->action(fn (Anime $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Anime  $anime
     * @param  array  $fields
     * @return void
     */
    public function handle(Anime $anime, array $fields): void
    {
        if ($anime->resources()->doesntExist()) {
            $this->failedLog(__('filament.actions.anime.backfill.message.resource_required_failure'));
            return;
        }

        $action = new BackfillAnimeAction($anime, $this->getToBackfill($fields));

        try {
            $result = $action->handle();
            // if ($result->hasFailed()) {
            //     Notification::make()
            //         ->body($result->getMessage())
            //         ->warning()
            //         ->actions([
            //             NotificationAction::make('mark-as-read')
            //                 ->button()
            //                 ->markAsRead(),
            //         ])
            //         ->sendToDatabase(Auth::user());
            // }
        } catch (Exception $e) {
            $this->failedLog($e);
        } finally {
            // Try not to upset third-party APIs
            Sleep::for(rand(3, 5))->second();
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): ?Form
    {
        $anime = $this->getRecord();

        return $form
            ->schema([
                Section::make(__('filament.actions.anime.backfill.fields.resources.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_KITSU_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.kitsu.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.kitsu.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU->value)->doesntExist()),

                        Checkbox::make(self::BACKFILL_ANILIST_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.anilist.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.anilist.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value)->doesntExist()),

                        Checkbox::make(self::BACKFILL_MAL_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.mal.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.mal.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL->value)->doesntExist()),

                        Checkbox::make(self::BACKFILL_ANIDB_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.anidb.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.anidb.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB->value)->doesntExist()),

                        Checkbox::make(self::BACKFILL_ANN_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.ann.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.ann.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANN->value)->doesntExist()),

                        Checkbox::make(self::BACKFILL_ANIME_PLANET_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.anime_planet.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.anime_planet.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIME_PLANET->value)->doesntExist()),

                        Checkbox::make(self::BACKFILL_OTHER_RESOURCES)
                            ->label(__('filament.actions.anime.backfill.fields.resources.external_links.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.external_links.help'))
                            ->default(fn () => $anime instanceof Anime),
                    ]),

                Section::make(__('filament.actions.anime.backfill.fields.images.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_LARGE_COVER)
                            ->label(__('filament.actions.anime.backfill.fields.images.large_cover.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.images.large_cover.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE->value)->doesntExist()),

                        Checkbox::make(self::BACKFILL_SMALL_COVER)
                            ->label(__('filament.actions.anime.backfill.fields.images.small_cover.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.images.small_cover.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_SMALL->value)->doesntExist()),
                    ]),

                Section::make(__('filament.actions.anime.backfill.fields.studios.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_STUDIOS)
                            ->label(__('filament.actions.anime.backfill.fields.studios.anime.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.studios.anime.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->studios()->doesntExist()),
                    ]),

                Section::make(__('filament.actions.anime.backfill.fields.synonyms.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_SYNONYMS)
                            ->label(__('filament.actions.anime.backfill.fields.synonyms.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.synonyms.help'))
                            ->default(fn () => $anime instanceof Anime && $anime->animesynonyms()->count() === 0),
                    ]),
            ]);
    }

    /**
     * Get what should be backfilled.
     *
     * @param  array  $fields
     * @return array
     */
    protected function getToBackfill(array $fields): array
    {
        $toBackfill = [];
        $toBackfill[self::RESOURCES] = [];
        $toBackfill[self::IMAGES] = [];

        foreach ($this->getResourcesMapping() as $field => $sites) {
            if (Arr::get($fields, $field) === true) {
                $toBackfill[self::RESOURCES] = array_merge($toBackfill[self::RESOURCES], $sites);
            }
        }

        foreach ($this->getImagesMapping() as $field => $facets) {
            if (Arr::get($fields, $field) === true) {
                $toBackfill[self::IMAGES] = array_merge($toBackfill[self::IMAGES], $facets);
            }
        }

        $toBackfill[self::STUDIOS] = Arr::get($fields, self::BACKFILL_STUDIOS);
        $toBackfill[self::SYNONYMS] = Arr::get($fields, self::BACKFILL_SYNONYMS);

        return $toBackfill;
    }

    /**
     * Get the resources for mapping.
     *
     * @return array
     */
    protected function getResourcesMapping(): array
    {
        return [
            self::BACKFILL_KITSU_RESOURCE => [ResourceSite::KITSU],
            self::BACKFILL_ANILIST_RESOURCE => [ResourceSite::ANILIST],
            self::BACKFILL_MAL_RESOURCE => [ResourceSite::MAL],
            self::BACKFILL_ANIDB_RESOURCE => [ResourceSite::ANIDB],
            self::BACKFILL_ANN_RESOURCE => [ResourceSite::ANN],
            self::BACKFILL_ANIME_PLANET_RESOURCE => [ResourceSite::ANIME_PLANET],
            self::BACKFILL_OTHER_RESOURCES => [
                ResourceSite::TWITTER, ResourceSite::OFFICIAL_SITE, ResourceSite::NETFLIX, ResourceSite::CRUNCHYROLL,
                ResourceSite::HIDIVE, ResourceSite::AMAZON_PRIME_VIDEO, ResourceSite::HULU, ResourceSite::DISNEY_PLUS,
            ],
        ];
    }

    /**
     * Get the images for mapping.
     *
     * @return array
     */
    protected function getImagesMapping(): array
    {
        return [
            self::BACKFILL_LARGE_COVER => [ImageFacet::COVER_LARGE],
            self::BACKFILL_SMALL_COVER => [ImageFacet::COVER_SMALL],
        ];
    }
}
