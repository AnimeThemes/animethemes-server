<?php

declare(strict_types=1);

namespace App\Nova\Resources\Admin;

use App\Models\Admin\Feature as FeatureModel;
use App\Nova\Resources\BaseResource;
use Exception;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Feature.
 */
class Feature extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = FeatureModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = FeatureModel::ATTRIBUTE_NAME;

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.resources.group.admin');
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
        return __('nova.resources.label.features');
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
        return __('nova.resources.singularLabel.feature');
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
            new Column(FeatureModel::ATTRIBUTE_NAME),
        ];
    }

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Determine if this resource uses Laravel Scout.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function usesScout(): bool
    {
        return false;
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
            ID::make(__('nova.fields.base.id'), FeatureModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.feature.key.name'), FeatureModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->readonly()
                ->help(__('nova.fields.feature.key.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.setting.value.name'), FeatureModel::ATTRIBUTE_VALUE)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.fields.feature.value.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),
        ];
    }
}
