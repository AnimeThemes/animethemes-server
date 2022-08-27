<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use App\Nova\Resources\Wiki\Song;
use BenSampo\Enum\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Theme.
 */
class Theme extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = AnimeTheme::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = AnimeTheme::ATTRIBUTE_SLUG;

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        $theme = $this->model();
        if ($theme instanceof AnimeTheme) {
            return $theme->anime->getName();
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
        return __('nova.anime_themes');
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
        return __('nova.anime_theme');
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
        return 'anime-themes';
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
            new Column(AnimeTheme::ATTRIBUTE_SLUG),
        ];
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
        return $query->with([AnimeTheme::RELATION_ANIME, AnimeTheme::RELATION_SONG]);
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
    public static function relatableQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(AnimeTheme::RELATION_ANIME);
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
            ID::make(__('nova.id'), AnimeTheme::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            BelongsTo::make(__('nova.anime'), AnimeTheme::RELATION_ANIME, Anime::class)
                ->sortable()
                ->filterable()
                ->searchable(fn () => $request->viaResource() === null || Song::class === $request->viaResource())
                ->readonly(fn () => $request->viaResource() !== null && Song::class !== $request->viaResource())
                ->required(fn () => $request->viaResource() === null || Song::class === $request->viaResource())
                ->withSubtitles()
                ->showCreateRelationButton(fn () => $request->viaResource() === null || Song::class === $request->viaResource())
                ->showOnPreview(),

            Select::make(__('nova.type'), AnimeTheme::ATTRIBUTE_TYPE)
                ->options(ThemeType::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', new EnumValue(ThemeType::class, false)])
                ->help(__('nova.anime_theme_type_help'))
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.sequence'), AnimeTheme::ATTRIBUTE_SEQUENCE)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.anime_theme_sequence_help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.group'), AnimeTheme::ATTRIBUTE_GROUP)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.anime_theme_group_help'))
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.slug'), AnimeTheme::ATTRIBUTE_SLUG)
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->help(__('nova.anime_theme_slug_help'))
                ->showOnPreview()
                ->filterable()
                ->dependsOn(
                    [AnimeTheme::ATTRIBUTE_TYPE, AnimeTheme::ATTRIBUTE_SEQUENCE],
                    function (Text $field, NovaRequest $novaRequest, FormData $formData) {
                        $slug = Str::of('');
                        if ($formData->offsetExists(AnimeTheme::ATTRIBUTE_TYPE)) {
                            $type = ThemeType::getKey(intval($formData->offsetGet(AnimeTheme::ATTRIBUTE_TYPE)));
                            $slug = $slug->append($type);
                        }
                        if ($slug->isNotEmpty() && $formData->offsetExists(AnimeTheme::ATTRIBUTE_SEQUENCE)) {
                            $slug = $slug->append($formData->offsetGet(AnimeTheme::ATTRIBUTE_SEQUENCE));
                        }
                        $field->value = $slug->__toString();
                    }
                ),

            BelongsTo::make(__('nova.song'), AnimeTheme::RELATION_SONG, Song::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showCreateRelationButton()
                ->showOnPreview(),

            HasMany::make(__('nova.anime_theme_entries'), AnimeTheme::RELATION_ENTRIES, Entry::class),

            Panel::make(__('nova.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}
