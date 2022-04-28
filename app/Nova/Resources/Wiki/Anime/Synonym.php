<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeSynonym;
use App\Nova\Resources\Resource;
use App\Nova\Resources\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Synonym.
 */
class Synonym extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = AnimeSynonym::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = AnimeSynonym::ATTRIBUTE_TEXT;

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
        return __('nova.anime_synonyms');
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
        return __('nova.anime_synonym');
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
        return 'anime-synonyms';
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
            new Column(AnimeSynonym::ATTRIBUTE_TEXT),
        ];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(AnimeSynonym::RELATION_ANIME);
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
            BelongsTo::make(__('nova.anime'), AnimeSynonym::RELATION_ANIME, Anime::class)
                ->sortable()
                ->filterable()
                ->searchable(fn () => $request->viaResource() === null)
                ->readonly(fn () => $request->viaResource() !== null)
                ->required(fn () => $request->viaResource() === null)
                ->withSubtitles()
                ->showCreateRelationButton(fn () => $request->viaResource() === null)
                ->showOnPreview(),

            ID::make(__('nova.id'), AnimeSynonym::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.text'), AnimeSynonym::ATTRIBUTE_TEXT)
                ->sortable()
                ->rules(['required', 'max:192'])
                ->help(__('nova.anime_synonym_text_help'))
                ->showOnPreview()
                ->filterable(),

            Panel::make(__('nova.timestamps'), $this->timestamps()),
        ];
    }
}
