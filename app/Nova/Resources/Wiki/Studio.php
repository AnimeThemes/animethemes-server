<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Nova\Lenses\Studio\StudioUnlinkedLens;
use App\Nova\Resources\Resource;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

/**
 * Class Studio.
 */
class Studio extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \App\Models\Wiki\Studio::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.wiki');
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
        return __('nova.studios');
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
        return __('nova.studio');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), 'studio_id')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.studio_name_help')),

            Slug::make(__('nova.slug'), 'slug')
                ->from('name')
                ->separator('_')
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->updateRules('unique:studios,slug,{{resourceId}},studio_id')
                ->help(__('nova.studio_slug_help')),

            BelongsToMany::make(__('nova.anime'), 'Anime', Anime::class)
                ->searchable()
                ->fields(function () {
                    return [
                        DateTime::make(__('nova.created_at'), 'created_at')
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), 'updated_at')
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            AuditableLog::make(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new StudioUnlinkedLens(),
            ]
        );
    }
}
