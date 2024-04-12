<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Anime;

use App\Actions\Models\BackfillAction;
use App\Actions\Models\Wiki\Anime\BackfillAnimeOtherResourcesAction;
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
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Sleep;

/**
 * Class BackfillAnimeHeaderAction.
 */
class BackfillAnimeHeaderAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const BACKFILL_ANIDB_RESOURCE = 'backfill_anidb_resource';
    final public const BACKFILL_ANILIST_RESOURCE = 'backfill_anilist_resource';
    final public const BACKFILL_ANN_RESOURCE = 'backfill_ann_resource';
    final public const BACKFILL_KITSU_RESOURCE = 'backfill_kitsu_resource';
    final public const BACKFILL_OTHER_RESOURCES = 'backfill_other_resources';
    final public const BACKFILL_LARGE_COVER = 'backfill_large_cover';
    final public const BACKFILL_MAL_RESOURCE = 'backfill_mal_resource';
    final public const BACKFILL_SMALL_COVER = 'backfill_small_cover';
    final public const BACKFILL_STUDIOS = 'backfill_studios';

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Model $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Model  $anime
     * @param  array  $fields
     * @return void
     */
    public function handle(Model $anime, array $fields): void
    {
        if (!$anime instanceof Anime) return;

        if ($anime->resources()->doesntExist()) {
            $this->fail(__('filament.actions.anime.backfill.message.resource_required_failure'));
            return;
        }
        
        $actions = $this->getActions($fields, $anime);

        try {
            foreach ($actions as $action) {
                $result = $action->handle();
                if ($result->hasFailed()) {
                    Notification::make()
                        ->body($result->getMessage())
                        ->warning()
                        ->actions([
                            NotificationAction::make('mark-as-read')
                                ->button()
                                ->markAsRead(),
                        ])
                        ->sendToDatabase(auth()->user());
                }
            }
        } catch (Exception $e) {
            $this->fail($e);
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
            ]);
    }

    /**
     * Get the selected actions for backfilling anime.
     *
     * @param  array  $fields
     * @param  Anime  $anime
     * @return BackfillAction[]
     */
    protected function getActions(array $fields, Anime $anime): array
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
            self::BACKFILL_OTHER_RESOURCES => new BackfillAnimeOtherResourcesAction($anime),
            self::BACKFILL_LARGE_COVER => new BackfillLargeCoverImageAction($anime),
            self::BACKFILL_SMALL_COVER => new BackfillSmallCoverImageAction($anime),
            self::BACKFILL_STUDIOS => new BackfillAnimeStudiosAction($anime),
        ];
    }
}
