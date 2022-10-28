<?php

declare(strict_types=1);

namespace App\Nova\Resources\Admin;

use App\Models\Admin\Setting as SettingModel;
use App\Nova\Resources\BaseResource;
use Exception;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Setting.
 */
class Setting extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = SettingModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = SettingModel::ATTRIBUTE_KEY;

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
        return __('nova.resources.label.settings');
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
        return __('nova.resources.singularLabel.setting');
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
            new Column(SettingModel::ATTRIBUTE_KEY),
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
            ID::make(__('nova.fields.base.id'), SettingModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.setting.key'), SettingModel::ATTRIBUTE_KEY)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->showWhenPeeking(),

            Text::make(__('nova.fields.setting.value'), SettingModel::ATTRIBUTE_VALUE)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:65535'])
                ->showOnPreview()
                ->filterable()
                ->maxlength(65535)
                ->showWhenPeeking(),
        ];
    }
}
