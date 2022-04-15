<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use App\Nova\Lenses\ExternalResource\ExternalResourceUnlinkedLens;
use App\Nova\Resources\Resource;
use App\Pivots\AnimeResource;
use App\Pivots\ArtistResource;
use App\Pivots\BasePivot;
use App\Pivots\StudioResource;
use App\Rules\Wiki\ResourceLinkMatchesSiteRule;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

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
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        return (string) data_get($this, ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID);
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
        return __('nova.wiki');
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
            new Column(ExternalResourceModel::ATTRIBUTE_LINK),
            new Column(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID),
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
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), ExternalResourceModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Select::make(__('nova.site'), ExternalResourceModel::ATTRIBUTE_SITE)
                ->options(ResourceSite::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', (new EnumValue(ResourceSite::class, false))->__toString()])
                ->help(__('nova.resource_site_help'))
                ->showOnPreview()
                ->filterable(),

            URL::make(__('nova.link'), ExternalResourceModel::ATTRIBUTE_LINK)
                ->sortable()
                ->rules(['required', 'max:192', 'url', new ResourceLinkMatchesSiteRule(intval($request->input('site')))])
                ->creationRules(Rule::unique(ExternalResourceModel::TABLE)->__toString())
                ->updateRules(
                    Rule::unique(ExternalResourceModel::TABLE)
                        ->ignore($request->route('resourceId'), ExternalResourceModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->displayUsing(fn (mixed $value, mixed $resource, string $attribute) => $value)
                ->help(__('nova.resource_link_help'))
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.external_id'), ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                ->nullable()
                ->sortable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.resource_external_id_help'))
                ->showOnPreview()
                ->filterable(),

            BelongsToMany::make(__('nova.artists'), ExternalResourceModel::RELATION_ARTISTS, Artist::class)
                ->searchable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.as'), ArtistResource::ATTRIBUTE_AS)
                        ->nullable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.resource_as_help')),

                    DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            BelongsToMany::make(__('nova.anime'), ExternalResourceModel::RELATION_ANIME, Anime::class)
                ->searchable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.as'), AnimeResource::ATTRIBUTE_AS)
                        ->nullable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.resource_as_help')),

                    DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            BelongsToMany::make(__('nova.studios'), ExternalResourceModel::RELATION_STUDIOS, Studio::class)
                ->searchable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(function () {
                    return [
                        Text::make(__('nova.as'), StudioResource::ATTRIBUTE_AS)
                            ->nullable()
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.resource_as_help')),

                        DateTime::make(__('nova.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->hideWhenCreating()
                            ->hideWhenUpdating(),

                        DateTime::make(__('nova.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->hideWhenCreating()
                            ->hideWhenUpdating(),
                    ];
                }),

            Panel::make(__('nova.timestamps'), $this->timestamps()),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new ExternalResourceUnlinkedLens(),
            ]
        );
    }
}
