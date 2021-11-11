<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use App\Nova\Filters\Wiki\ExternalResource\ExternalResourceSiteFilter;
use App\Nova\Lenses\ExternalResource\ExternalResourceUnlinkedLens;
use App\Nova\Resources\Resource;
use App\Pivots\AnimeResource;
use App\Pivots\ArtistResource;
use App\Pivots\BasePivot;
use App\Pivots\StudioResource;
use App\Rules\Wiki\ResourceSiteDomainRule;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

/**
 * Class ExternalResource.
 */
class ExternalResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = ExternalResourceModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = ExternalResourceModel::ATTRIBUTE_LINK;

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        ExternalResourceModel::ATTRIBUTE_LINK,
    ];

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
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.external_resources');
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
        return __('nova.external_resource');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('nova.id'), ExternalResourceModel::ATTRIBUTE_ID)
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),

            Select::make(__('nova.site'), ExternalResourceModel::ATTRIBUTE_SITE)
                ->options(ResourceSite::asSelectArray())
                ->displayUsing(function (?Enum $enum) {
                    return $enum?->description;
                })
                ->sortable()
                ->rules(['required', (new EnumValue(ResourceSite::class, false))->__toString()])
                ->help(__('nova.resource_site_help')),

            Url::make(__('nova.link'), 'link')
                ->sortable()
                ->rules(['required', 'max:192', 'url', new ResourceSiteDomainRule(intval($request->input('site')))])
                ->creationRules(Rule::unique(ExternalResourceModel::TABLE)->__toString())
                ->updateRules(
                    Rule::unique(ExternalResourceModel::TABLE)
                        ->ignore($request->resourceId, ExternalResourceModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->help(__('nova.resource_link_help'))
                ->alwaysClickable(),

            Number::make(__('nova.external_id'), ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                ->nullable()
                ->sortable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.resource_external_id_help')),

            BelongsToMany::make(__('nova.artists'), 'Artists', Artist::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), ArtistResource::ATTRIBUTE_AS)
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.anime'), 'Anime', Anime::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), AnimeResource::ATTRIBUTE_AS)
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            BelongsToMany::make(__('nova.studios'), 'Studios', Studio::class)
                ->searchable()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), StudioResource::ATTRIBUTE_AS)
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->readonly()
                            ->hideWhenCreating(),
                    ];
                }),

            AuditableLog::make(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return array_merge(
            [
                new ExternalResourceSiteFilter(),
            ],
            parent::filters($request)
        );
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function lenses(Request $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new ExternalResourceUnlinkedLens(),
            ]
        );
    }
}
