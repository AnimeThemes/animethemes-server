<?php

declare(strict_types=1);

namespace App\Nova\Resources\Admin;

use App\Models\Admin\Announcement as AnnouncementModel;
use App\Nova\Resources\BaseResource;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Announcement.
 */
class Announcement extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = AnnouncementModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = AnnouncementModel::ATTRIBUTE_ID;

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
        return __('nova.resources.label.announcements');
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
        return __('nova.resources.singularLabel.announcement');
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
            new Column(AnnouncementModel::ATTRIBUTE_ID),
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
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), AnnouncementModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Code::make(__('nova.fields.announcement.content'), AnnouncementModel::ATTRIBUTE_CONTENT)
                ->rules(['required', 'max:65535'])
                ->language('htmlmixed')
                ->showOnPreview(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps()),
        ];
    }

    /**
     * Get the fields displayed by the resource on index page.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function fieldsForIndex(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), AnnouncementModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.fields.announcement.content'), AnnouncementModel::ATTRIBUTE_CONTENT)
                ->sortable()
                ->showOnPreview(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}
