<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use App\Nova\Lenses\ExternalResource\ExternalResourceUnlinkedLens;
use App\Nova\Resources\BaseResource;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\SongResource;
use App\Pivots\Wiki\StudioResource;
use App\Rules\Wiki\Resource\ResourceLinkFormatRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\FormData;
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
class ExternalResource extends BaseResource
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
        return __('nova.resources.group.wiki');
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
        return __('nova.resources.label.external_resources');
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
        return __('nova.resources.singularLabel.external_resource');
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
            ID::make(__('nova.fields.base.id'), ExternalResourceModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Select::make(__('nova.fields.external_resource.site.name'), ExternalResourceModel::ATTRIBUTE_SITE)
                ->options(ResourceSite::asSelectArray())
                ->displayUsing(fn (?int $enumValue) => ResourceSite::tryFrom($enumValue)?->localize())
                ->sortable()
                ->rules(['required', new Enum(ResourceSite::class)])
                ->help(__('nova.fields.external_resource.site.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking()
                ->dependsOn(
                    [ExternalResourceModel::ATTRIBUTE_LINK],
                    function (Select $field, NovaRequest $novaRequest, FormData $formData) {
                        if ($formData->offsetExists(ExternalResourceModel::ATTRIBUTE_LINK)) {
                            $link = $formData->offsetGet(ExternalResourceModel::ATTRIBUTE_LINK);
                            $site = ResourceSite::valueOf($link);
                            $field->value = $site?->value ?? ResourceSite::OFFICIAL_SITE;
                        }
                    }
                ),

            URL::make(__('nova.fields.external_resource.link.name'), ExternalResourceModel::ATTRIBUTE_LINK)
                ->sortable()
                ->rules(['required', 'max:192', 'url', new ResourceLinkFormatRule()])
                ->creationRules(Rule::unique(ExternalResourceModel::class)->__toString())
                ->updateRules(
                    Rule::unique(ExternalResourceModel::class)
                        ->ignore($request->route('resourceId'), ExternalResourceModel::ATTRIBUTE_ID)
                        ->__toString()
                )
                ->displayUsing(fn (mixed $value, mixed $resource, string $attribute) => $value)
                ->help(__('nova.fields.external_resource.link.help'))
                ->showOnPreview()
                ->showWhenPeeking()
                ->filterable(),

            Number::make(__('nova.fields.external_resource.external_id.name'), ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                ->nullable()
                ->sortable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.fields.external_resource.external_id.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking()
                ->dependsOn(
                    [ExternalResourceModel::ATTRIBUTE_LINK],
                    function (Text $field, NovaRequest $novaRequest, FormData $formData) {
                        if ($formData->offsetExists(ExternalResourceModel::ATTRIBUTE_LINK)) {
                            $link = $formData->offsetGet(ExternalResourceModel::ATTRIBUTE_LINK);
                            $field->value = ResourceSite::parseIdFromLink($link);
                        }
                    }
                ),

            BelongsToMany::make(__('nova.resources.label.artists'), ExternalResourceModel::RELATION_ARTISTS, Artist::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.fields.artist.resources.as.name'), ArtistResource::ATTRIBUTE_AS)
                        ->nullable()
                        ->copyable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.fields.artist.resources.as.help')),

                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            BelongsToMany::make(__('nova.resources.label.anime'), ExternalResourceModel::RELATION_ANIME, Anime::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.fields.anime.resources.as.name'), AnimeResource::ATTRIBUTE_AS)
                        ->nullable()
                        ->copyable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.fields.anime.resources.as.help')),

                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            BelongsToMany::make(__('nova.resources.label.songs'), ExternalResourceModel::RELATION_SONGS, Song::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(fn () => [
                    Text::make(__('nova.fields.song.resources.as.name'), SongResource::ATTRIBUTE_AS)
                        ->nullable()
                        ->copyable()
                        ->rules(['nullable', 'max:192'])
                        ->help(__('nova.fields.song.resources.as.help')),

                    DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),

                    DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->hideWhenCreating()
                        ->hideWhenUpdating(),
                ]),

            BelongsToMany::make(__('nova.resources.label.studios'), ExternalResourceModel::RELATION_STUDIOS, Studio::class)
                ->searchable()
                ->filterable()
                ->withSubtitles()
                ->showCreateRelationButton()
                ->fields(function () {
                    return [
                        Text::make(__('nova.fields.studio.resources.as.name'), StudioResource::ATTRIBUTE_AS)
                            ->nullable()
                            ->copyable()
                            ->rules(['nullable', 'max:192'])
                            ->help(__('nova.fields.studio.resources.as.help')),

                        DateTime::make(__('nova.fields.base.created_at'), BasePivot::ATTRIBUTE_CREATED_AT)
                            ->hideWhenCreating()
                            ->hideWhenUpdating(),

                        DateTime::make(__('nova.fields.base.updated_at'), BasePivot::ATTRIBUTE_UPDATED_AT)
                            ->hideWhenCreating()
                            ->hideWhenUpdating(),
                    ];
                }),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
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
