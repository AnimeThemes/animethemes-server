<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video as VideoModel;
use App\Models\Wiki\Video\VideoScript;
use App\Nova\Actions\Models\Wiki\Video\BackfillAudioAction;
use App\Nova\Actions\Repositories\Storage\Wiki\Video\ReconcileVideoAction;
use App\Nova\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Nova\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Nova\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Nova\Actions\Storage\Wiki\Video\UploadVideoAction;
use App\Nova\Lenses\Video\VideoAudioLens;
use App\Nova\Lenses\Video\VideoResolutionLens;
use App\Nova\Lenses\Video\VideoScriptLens;
use App\Nova\Lenses\Video\VideoSourceLens;
use App\Nova\Lenses\Video\VideoUnlinkedLens;
use App\Nova\Metrics\Video\NewVideos;
use App\Nova\Metrics\Video\VideosPerDay;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\List\Playlist\Track;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use App\Nova\Resources\Wiki\Video\Script;
use App\Pivots\BasePivot;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Exception;
use Laravel\Nova\Card;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Video.
 */
class Video extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = VideoModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = VideoModel::ATTRIBUTE_FILENAME;

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.resources.group.wiki');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.resources.label.videos');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function singularLabel(): string
    {
        return __('nova.resources.singularLabel.video');
    }

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new Column(VideoModel::ATTRIBUTE_FILENAME),
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), VideoModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Number::make(__('nova.fields.video.resolution.name'), VideoModel::ATTRIBUTE_RESOLUTION)
                ->sortable()
                ->min(360)
                ->max(1080)
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.fields.video.resolution.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Boolean::make(__('nova.fields.video.nc.name'), VideoModel::ATTRIBUTE_NC)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.fields.video.nc.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Boolean::make(__('nova.fields.video.subbed.name'), VideoModel::ATTRIBUTE_SUBBED)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.fields.video.subbed.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Boolean::make(__('nova.fields.video.lyrics.name'), VideoModel::ATTRIBUTE_LYRICS)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.fields.video.lyrics.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Boolean::make(__('nova.fields.video.uncen.name'), VideoModel::ATTRIBUTE_UNCEN)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'boolean'])
                ->help(__('nova.fields.video.uncen.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.video.overlap.name'), VideoModel::ATTRIBUTE_OVERLAP)
                ->options(VideoOverlap::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->nullable()
                ->sortable()
                ->rules(['nullable', new EnumValue(VideoOverlap::class, false)])
                ->help(__('nova.fields.video.overlap.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.video.source.name'), VideoModel::ATTRIBUTE_SOURCE)
                ->options(VideoSource::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->nullable()
                ->sortable()
                ->rules(['nullable', new EnumValue(VideoSource::class, false)])
                ->help(__('nova.fields.video.source.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.resources.singularLabel.audio'), VideoModel::RELATION_AUDIO, Audio::class)
                ->hideFromIndex()
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->showOnPreview(),

            BelongsToMany::make(__('nova.resources.label.anime_theme_entries'), VideoModel::RELATION_ANIMETHEMEENTRIES, Entry::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->fields(fn () => [
                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating(),
                ]),

            HasOne::make(__('nova.resources.singularLabel.video_script'), VideoModel::RELATION_SCRIPT, Script::class)
                ->hideFromIndex()
                ->sortable()
                ->nullable(),

            HasMany::make(__('nova.resources.label.playlist_tracks'), VideoModel::RELATION_TRACKS, Track::class),

            Panel::make(__('nova.fields.base.file_properties'), $this->fileProperties())
                ->collapsable(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    protected function fileProperties(): array
    {
        return [
            Text::make(__('nova.fields.video.basename.name'), VideoModel::ATTRIBUTE_BASENAME)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.video.filename.name'), VideoModel::ATTRIBUTE_FILENAME)
                ->sortable()
                ->copyable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.video.path.name'), VideoModel::ATTRIBUTE_PATH)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            Number::make(__('nova.fields.video.size.name'), VideoModel::ATTRIBUTE_SIZE)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.video.mimetype.name'), VideoModel::ATTRIBUTE_MIMETYPE)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return array_merge(
            parent::actions($request),
            [
                (new BackfillAudioAction($request->user()))
                    ->confirmButtonText(__('nova.actions.video.backfill.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline()
                    ->canSeeWhen('update', $this),

                (new UploadVideoAction())
                    ->confirmButtonText(__('nova.actions.storage.upload.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSeeWhen('create', VideoModel::class),

                (new MoveVideoAction())
                    ->confirmButtonText(__('nova.actions.storage.move.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->exceptOnIndex()
                    ->canSeeWhen('create', VideoModel::class),

                (new DeleteVideoAction())
                    ->confirmText(__('nova.actions.video.delete.confirmText'))
                    ->confirmButtonText(__('nova.actions.storage.delete.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->exceptOnIndex()
                    ->canSeeWhen('delete', $this),

                (new ReconcileVideoAction())
                    ->confirmButtonText(__('nova.actions.repositories.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSeeWhen('create', VideoModel::class),

                (new UploadScriptAction())
                    ->confirmButtonText(__('nova.actions.storage.upload.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnDetail()
                    ->canSeeWhen('create', VideoScript::class),
            ]
        );
    }

    /**
     * Get the cards available for the request.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request): array
    {
        return array_merge(
            parent::cards($request),
            [
                (new NewVideos())->width(Card::ONE_HALF_WIDTH),
                (new VideosPerDay())->width(Card::ONE_HALF_WIDTH),
            ]
        );
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new VideoAudioLens(),
                new VideoResolutionLens(),
                new VideoSourceLens(),
                new VideoUnlinkedLens(),
                new VideoScriptLens(),
            ],
        );
    }
}
