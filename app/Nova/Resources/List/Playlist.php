<?php

declare(strict_types=1);

namespace App\Nova\Resources\List;

use App\Contracts\Models\HasHashids;
use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist as PlaylistModel;
use App\Nova\Actions\Models\AssignHashidsAction;
use App\Nova\Resources\Auth\User;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\List\Playlist\Track;
use App\Nova\Resources\Wiki\Image;
use App\Pivots\BasePivot;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Playlist.
 */
class Playlist extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = PlaylistModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = PlaylistModel::ATTRIBUTE_NAME;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        $playlist = $this->model();
        if ($playlist instanceof PlaylistModel) {
            return $playlist->user->getName();
        }

        return null;
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.resources.group.list');
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
        return __('nova.resources.label.playlists');
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
        return __('nova.resources.singularLabel.playlist');
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
            new Column(PlaylistModel::ATTRIBUTE_NAME),
        ];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(PlaylistModel::RELATION_USER);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function relatableQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(PlaylistModel::RELATION_USER);
    }

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

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
            BelongsTo::make(__('nova.resources.singularLabel.user'), PlaylistModel::RELATION_USER, User::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showOnPreview(),

            ID::make(__('nova.fields.base.id'), PlaylistModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.playlist.name.name'), PlaylistModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.fields.playlist.name.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.playlist.visibility.name'), PlaylistModel::ATTRIBUTE_VISIBILITY)
                ->options(PlaylistVisibility::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', new EnumValue(PlaylistVisibility::class, false)])
                ->help(__('nova.fields.playlist.visibility.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.playlist.hashid.name'), HasHashids::ATTRIBUTE_HASHID)
                ->readonly()
                ->sortable()
                ->copyable()
                ->help(__('nova.fields.playlist.hashid.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.fields.playlist.first.name'), PlaylistModel::RELATION_FIRST, Track::class)
                ->hideFromIndex()
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->showOnPreview(),

            BelongsTo::make(__('nova.fields.playlist.last.name'), PlaylistModel::RELATION_LAST, Track::class)
                ->hideFromIndex()
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->showOnPreview(),

            BelongsToMany::make(__('nova.resources.label.images'), PlaylistModel::RELATION_IMAGES, Image::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating(),
                ]),

            HasMany::make(__('nova.resources.label.playlist_tracks'), PlaylistModel::RELATION_TRACKS, Track::class),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
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
                (new AssignHashidsAction('playlists'))
                    ->confirmButtonText(__('nova.actions.models.assign_hashids.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->showOnIndex()
                    ->showOnDetail()
                    ->showInline()
                    ->canSeeWhen('update', $this),
            ]
        );
    }
}
