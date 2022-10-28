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
        return __('nova.resources.label.anime_themes');
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
        return __('nova.resources.singularLabel.anime_theme');
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
        return __('nova.resources.group.wiki');
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
            ID::make(__('nova.fields.base.id'), AnimeTheme::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.resources.singularLabel.anime'), AnimeTheme::RELATION_ANIME, Anime::class)
                ->sortable()
                ->filterable()
                ->searchable(fn () => $request->viaResource() === null || Song::class === $request->viaResource())
                ->readonly(fn () => $request->viaResource() !== null && Song::class !== $request->viaResource())
                ->required(fn () => $request->viaResource() === null || Song::class === $request->viaResource())
                ->withSubtitles()
                ->showCreateRelationButton(fn () => $request->viaResource() === null || Song::class === $request->viaResource())
                ->showOnPreview(),

            Select::make(__('nova.fields.anime_theme.type.name'), AnimeTheme::ATTRIBUTE_TYPE)
                ->options(ThemeType::asSelectArray())
                ->displayUsing(fn (?Enum $enum) => $enum?->description)
                ->sortable()
                ->rules(['required', new EnumValue(ThemeType::class, false)])
                ->help(__('nova.fields.anime_theme.type.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Number::make(__('nova.fields.anime_theme.sequence.name'), AnimeTheme::ATTRIBUTE_SEQUENCE)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'integer'])
                ->help(__('nova.fields.anime_theme.sequence.help'))
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.anime_theme.group.name'), AnimeTheme::ATTRIBUTE_GROUP)
                ->sortable()
                ->nullable()
                ->rules(['nullable', 'max:192'])
                ->help(__('nova.fields.anime_theme.group.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->showWhenPeeking(),

            Text::make(__('nova.fields.anime_theme.slug.name'), AnimeTheme::ATTRIBUTE_SLUG)
                ->sortable()
                ->rules(['required', 'max:192', 'alpha_dash'])
                ->help(__('nova.fields.anime_theme.slug.help'))
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->showWhenPeeking()
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

            BelongsTo::make(__('nova.resources.singularLabel.song'), AnimeTheme::RELATION_SONG, Song::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showCreateRelationButton()
                ->showOnPreview(),

            HasMany::make(__('nova.resources.label.anime_theme_entries'), AnimeTheme::RELATION_ENTRIES, Entry::class),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}
