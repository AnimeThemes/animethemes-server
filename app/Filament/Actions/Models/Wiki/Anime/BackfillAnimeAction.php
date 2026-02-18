<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\BackfillAnimeAction as BackfillAnime;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Actions\Base\MarkAsReadAction;
use App\Filament\Actions\BaseAction;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BackfillAnimeAction extends BaseAction
{
    final public const string RESOURCES = BackfillAnime::RESOURCES;
    final public const string IMAGES = BackfillAnime::IMAGES;
    final public const string STUDIOS = BackfillAnime::STUDIOS;
    final public const string SYNONYMS = BackfillAnime::SYNONYMS;

    final public const string BACKFILL_ANIDB_RESOURCE = 'backfill_anidb_resource';
    final public const string BACKFILL_ANILIST_RESOURCE = 'backfill_anilist_resource';
    final public const string BACKFILL_ANIME_PLANET_RESOURCE = 'backfill_anime_planet_resource';
    final public const string BACKFILL_ANN_RESOURCE = 'backfill_ann_resource';
    final public const string BACKFILL_KITSU_RESOURCE = 'backfill_kitsu_resource';
    final public const string BACKFILL_LIVECHART_RESOURCE = 'backfill_livechart_resource';
    final public const string BACKFILL_OFFICIAL_RESOURCES = 'backfill_official_resources';
    final public const string BACKFILL_LARGE_COVER = 'backfill_large_cover';
    final public const string BACKFILL_MAL_RESOURCE = 'backfill_mal_resource';
    final public const string BACKFILL_SMALL_COVER = 'backfill_small_cover';
    final public const string BACKFILL_STUDIOS = 'backfill_studios';
    final public const string BACKFILL_SYNONYMS = 'backfill_synonyms';

    public static function getDefaultName(): ?string
    {
        return 'backfill-anime';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.anime.backfill.name'));
        $this->icon(Heroicon::OutlinedBars4);

        $this->visible(Gate::allows('create', Anime::class));

        $this->action(fn (Anime $record, array $data) => $this->handle($record, $data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Anime $anime, array $data): void
    {
        if ($anime->resources()->doesntExist()) {
            $this->failedLog(__('filament.actions.anime.backfill.message.resource_required_failure'));

            return;
        }

        $action = new BackfillAnime($anime, $this->getToBackfill($data));

        $result = $action->handle();

        if ($result->hasFailed()) {
            Notification::make()
                ->body($result->getMessage())
                ->warning()
                ->actions([
                    MarkAsReadAction::make(),
                ])
                ->sendToDatabase(Auth::user());

            $this->failedLog($result->getMessage());
        }
    }

    /**
     * Get the schema available on the action.|null.
     */
    public function getSchema(Schema $schema): ?Schema
    {
        $anime = $this->getRecord();

        if (! ($anime instanceof Anime)) {
            return $schema;
        }

        $anime = $anime->load([Anime::RELATION_RESOURCES, Anime::RELATION_IMAGES, Anime::RELATION_STUDIOS, Anime::RELATION_ANIMESYNONYMS]);

        $resources = $anime->resources->pluck(ExternalResource::ATTRIBUTE_SITE)->keyBy(fn (ResourceSite $site) => $site->value)->keys();
        $images = $anime->images->pluck(Image::ATTRIBUTE_FACET)->keyBy(fn (ImageFacet $facet) => $facet->value)->keys();

        return $schema
            ->components([
                Section::make(__('filament.actions.anime.backfill.fields.resources.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_ANIDB_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.anidb.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.anidb.help'))
                            ->default(fn () => $resources->doesntContain(ResourceSite::ANIDB->value)),

                        Checkbox::make(self::BACKFILL_ANILIST_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.anilist.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.anilist.help'))
                            ->default(fn () => $resources->doesntContain(ResourceSite::ANILIST->value)),

                        Checkbox::make(self::BACKFILL_ANN_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.ann.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.ann.help'))
                            ->default(fn () => $resources->doesntContain(ResourceSite::ANN->value)),

                        Checkbox::make(self::BACKFILL_ANIME_PLANET_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.anime_planet.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.anime_planet.help'))
                            ->default(fn () => $resources->doesntContain(ResourceSite::ANIME_PLANET->value)),

                        Checkbox::make(self::BACKFILL_KITSU_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.kitsu.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.kitsu.help'))
                            ->default(fn () => $resources->doesntContain(ResourceSite::KITSU->value)),

                        Checkbox::make(self::BACKFILL_LIVECHART_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.livechart.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.livechart.help'))
                            ->default(fn () => $resources->doesntContain(ResourceSite::LIVECHART->value)),

                        Checkbox::make(self::BACKFILL_MAL_RESOURCE)
                            ->label(__('filament.actions.anime.backfill.fields.resources.mal.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.mal.help'))
                            ->default(fn () => $resources->doesntContain(ResourceSite::MAL->value)),

                        Checkbox::make(self::BACKFILL_OFFICIAL_RESOURCES)
                            ->label(__('filament.actions.anime.backfill.fields.resources.external_links.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.resources.external_links.help'))
                            ->default(true),
                    ]),

                Section::make(__('filament.actions.anime.backfill.fields.images.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_LARGE_COVER)
                            ->label(__('filament.actions.anime.backfill.fields.images.large_cover.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.images.large_cover.help'))
                            ->default(fn () => $images->doesntContain(ImageFacet::LARGE_COVER->value)),

                        Checkbox::make(self::BACKFILL_SMALL_COVER)
                            ->label(__('filament.actions.anime.backfill.fields.images.small_cover.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.images.small_cover.help'))
                            ->default(fn () => $images->doesntContain(ImageFacet::SMALL_COVER->value)),
                    ]),

                Section::make(__('filament.actions.anime.backfill.fields.studios.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_STUDIOS)
                            ->label(__('filament.actions.anime.backfill.fields.studios.anime.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.studios.anime.help'))
                            ->default(fn () => $anime->studios->isEmpty()),
                    ]),

                Section::make(__('filament.actions.anime.backfill.fields.synonyms.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_SYNONYMS)
                            ->label(__('filament.actions.anime.backfill.fields.synonyms.name'))
                            ->helperText(__('filament.actions.anime.backfill.fields.synonyms.help'))
                            ->default(fn () => $anime->synonyms->isEmpty()),
                    ]),
            ]);
    }

    /**
     * Get what should be backfilled.
     *
     * @param  array<string, mixed>  $components
     * @return array<string, array<int, ResourceSite|ImageFacet>|bool>
     */
    protected function getToBackfill(array $components): array
    {
        $toBackfill = [];
        $toBackfill[self::RESOURCES] = [];
        $toBackfill[self::IMAGES] = [];

        foreach ($this->getResourcesMapping() as $component => $sites) {
            if (Arr::get($components, $component) === true) {
                $toBackfill[self::RESOURCES] = array_merge($toBackfill[self::RESOURCES], $sites);
            }
        }

        foreach ($this->getImagesMapping() as $component => $facets) {
            if (Arr::get($components, $component) === true) {
                $toBackfill[self::IMAGES] = array_merge($toBackfill[self::IMAGES], $facets);
            }
        }

        $toBackfill[self::STUDIOS] = Arr::get($components, self::BACKFILL_STUDIOS);
        $toBackfill[self::SYNONYMS] = Arr::get($components, self::BACKFILL_SYNONYMS);

        return $toBackfill;
    }

    /**
     * Get the resources for mapping.
     *
     * @return array<string, array<int, ResourceSite>>
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
            self::BACKFILL_LIVECHART_RESOURCE => [ResourceSite::LIVECHART],
            self::BACKFILL_OFFICIAL_RESOURCES => [
                ResourceSite::X, ResourceSite::OFFICIAL_SITE, ResourceSite::NETFLIX, ResourceSite::CRUNCHYROLL,
                ResourceSite::HIDIVE, ResourceSite::AMAZON_PRIME_VIDEO, ResourceSite::HULU, ResourceSite::DISNEY_PLUS,
            ],
        ];
    }

    /**
     * Get the images for mapping.
     *
     * @return array<string, array<int, ImageFacet>>
     */
    protected function getImagesMapping(): array
    {
        return [
            self::BACKFILL_LARGE_COVER => [ImageFacet::LARGE_COVER],
            self::BACKFILL_SMALL_COVER => [ImageFacet::SMALL_COVER],
        ];
    }
}
