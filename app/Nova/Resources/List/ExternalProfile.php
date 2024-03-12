<?php

declare(strict_types=1);

namespace App\Nova\Resources\List;

use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\ExternalProfile as ExternalProfileModel;
use App\Nova\Resources\Auth\User;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\List\External\ExternalEntry;
use Illuminate\Validation\Rules\Enum;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class ExternalProfile.
 */
class ExternalProfile extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = ExternalProfileModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = ExternalProfileModel::ATTRIBUTE_USERNAME;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        $profile = $this->model();
        if ($profile instanceof ExternalProfileModel) {
            return $profile->getName();
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
        $profile = $this->model();
        if ($profile instanceof ExternalProfileModel) {
            return $profile->getName();
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
        return __('nova.resources.label.external_profiles');
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
        return __('nova.resources.singularLabel.external_profiles');
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
        return 'user-external-profile';
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
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new Column(ExternalProfileModel::ATTRIBUTE_USERNAME),
        ];
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
            ID::make(__('nova.fields.base.id'), ExternalProfileModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.external_profile.username.name'), ExternalProfileModel::ATTRIBUTE_USERNAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.fields.external_profile.username.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.external_profile.site.name'), ExternalProfileModel::ATTRIBUTE_SITE)
                ->options(ExternalProfileSite::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => ExternalProfileSite::tryFrom($enumValue)?->localize())
                ->sortable()
                ->rules(['required', new Enum(ExternalProfileSite::class)])
                ->help(__('nova.fields.external_profile.site.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.external_profile.visibility.name'), ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                ->options(ExternalProfileVisibility::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => ExternalProfileVisibility::tryFrom($enumValue)?->localize())
                ->sortable()
                ->rules(['required', new Enum(ExternalProfileVisibility::class)])
                ->help(__('nova.fields.external_profile.visibility.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.resources.singularLabel.user'), ExternalProfileModel::RELATION_USER, User::class)
                ->sortable()
                ->filterable()
                ->withSubtitles()
                ->showOnPreview(),

            HasMany::make(__('nova.resources.label.external_entries'), ExternalProfileModel::RELATION_EXTERNAL_ENTRIES, ExternalEntry::class),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
            ->collapsable(),
        ];
    }
}
