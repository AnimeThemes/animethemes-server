<?php

declare(strict_types=1);

namespace App\Nova\Resources\List\UserExternal;

use App\Enums\Models\List\AnimeWatchStatus;
use App\Models\List\UserExternalListEntry as UserExternalListEntryModel;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\List\UserExternalProfile;
use App\Nova\Resources\Wiki\Anime;
use Illuminate\Validation\Rules\Enum;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

/**
 * Class UserExternalListEntry.
 */
class UserExternalListEntry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = UserExternalListEntryModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = UserExternalListEntryModel::ATTRIBUTE_ANIME;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        $entry = $this->model();
        if ($entry instanceof UserExternalListEntryModel) {
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
        if ($entry instanceof UserExternalListEntry) {
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
        return __('nova.resources.label.user_external_list_entries');
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
        return __('nova.resources.singularLabel.user_external_list_entries');
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
            ID::make(__('nova.fields.base.id'), UserExternalListEntryModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.resources.singularLabel.anime'), UserExternalListEntryModel::RELATION_ANIME, Anime::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showCreateRelationButton()
                ->showOnPreview(),

            BelongsTo::make(__('nova.resources.singularLabel.user_external_profiles'), UserExternalListEntryModel::RELATION_USER_PROFILE, UserExternalProfile::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showCreateRelationButton()
                ->showOnPreview(),

            Number::make(__('nova.fields.user_external_list_entry.score.name'), UserExternalListEntryModel::ATTRIBUTE_SCORE)
                ->sortable()
                ->nullable()
                ->rules(['nullable'])
                ->step(0.01)
                ->help(__('nova.fields.user_external_list_entry.score.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.user_external_list_entry.watch_status.name'), UserExternalListEntryModel::ATTRIBUTE_WATCH_STATUS)
                ->options(AnimeWatchStatus::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => AnimeWatchStatus::tryFrom($enumValue)?->localize())
                ->sortable()
                ->rules(['required', new Enum(AnimeWatchStatus::class)])
                ->help(__('nova.fields.user_external_list_entry.watch_status.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}
