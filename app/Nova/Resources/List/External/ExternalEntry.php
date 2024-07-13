<?php

declare(strict_types=1);

namespace App\Nova\Resources\List\External;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry as ExternalEntryModel;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\List\ExternalProfile;
use App\Nova\Resources\Wiki\Anime;
use Illuminate\Validation\Rules\Enum;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

/**
 * Class ExternalEntry.
 */
class ExternalEntry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = ExternalEntryModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = ExternalEntryModel::ATTRIBUTE_ANIME;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        $entry = $this->model();
        if ($entry instanceof ExternalEntryModel) {
            return $entry->getName();
        }

        return parent::title();
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        $entry = $this->model();
        if ($entry instanceof ExternalEntryModel) {
            return $entry->getName();
        }

        return null;
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
        return __('nova.resources.label.externalentries');
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
        return __('nova.resources.singularLabel.externalentries');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function uriKey(): string
    {
        return 'user-external-list-entry';
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
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), ExternalEntryModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.resources.singularLabel.anime'), ExternalEntryModel::RELATION_ANIME, Anime::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->required()
                ->rules(['required'])
                ->withSubtitles()
                ->showOnPreview(),

            BelongsTo::make(__('nova.resources.singularLabel.externalprofiles'), ExternalEntryModel::RELATION_PROFILE, ExternalProfile::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->required()
                ->rules(['required'])
                ->withSubtitles()
                ->showCreateRelationButton()
                ->showOnPreview(),

            Boolean::make(__('nova.fields.externalentry.is_favorite.name'), ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                ->sortable()
                ->filterable()
                ->rules(['required'])
                ->help(__('nova.fields.externalentry.is_favorite.help'))
                ->showWhenPeeking()
                ->showOnPreview(),

            Number::make(__('nova.fields.externalentry.score.name'), ExternalEntryModel::ATTRIBUTE_SCORE)
                ->sortable()
                ->nullable()
                ->rules(['nullable'])
                ->step(0.01)
                ->help(__('nova.fields.externalentry.score.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.externalentry.watch_status.name'), ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                ->options(ExternalEntryWatchStatus::asSelectArray())
                ->displayUsing(fn(?int $enumValue) => ExternalEntryWatchStatus::tryFrom($enumValue)?->localize())
                ->sortable()
                ->rules(['required', new Enum(ExternalEntryWatchStatus::class)])
                ->help(__('nova.fields.externalentry.watch_status.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}
