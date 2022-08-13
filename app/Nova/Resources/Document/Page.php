<?php

declare(strict_types=1);

namespace App\Nova\Resources\Document;

use App\Models\Document\Page as PageModel;
use App\Nova\Resources\BaseResource;
use Exception;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Page.
 */
class Page extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = PageModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = PageModel::ATTRIBUTE_NAME;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        return (string) data_get($this, PageModel::ATTRIBUTE_SLUG);
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
        return __('nova.document');
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
        return __('nova.pages');
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
        return __('nova.page');
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
            new Column(PageModel::ATTRIBUTE_NAME),
            new Column(PageModel::ATTRIBUTE_SLUG),
        ];
    }

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
            ID::make(__('nova.id'), PageModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.name'), PageModel::ATTRIBUTE_NAME)
                ->sortable()
                ->copyable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.page_name_help'))
                ->showOnPreview()
                ->filterable(),

            Slug::make(__('nova.slug'), PageModel::ATTRIBUTE_SLUG)
                ->from(PageModel::ATTRIBUTE_NAME)
                ->separator('_')
                ->sortable()
                ->rules(['required', 'max:192', 'regex:/^[\pL\pM\pN\/_-]+$/u'])
                ->creationRules(Rule::unique(PageModel::TABLE)->__toString())
                ->updateRules(
                    Rule::unique(PageModel::TABLE)
                        ->ignore($request->route('resourceId'), PageModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->help(__('nova.page_slug_help'))
                ->showOnPreview(),

            Markdown::make(__('nova.body'), PageModel::ATTRIBUTE_BODY)
                ->rules(['required', 'max:16777215'])
                ->help(__('nova.page_body_help')),

            Panel::make(__('nova.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}
