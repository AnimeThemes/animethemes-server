<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

/**
 * Class EntryShowTest.
 */
class EntryShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Entry Show Endpoint shall return an Entry Resource.
     *
     * @return void
     */
    public function test_default(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall return an Entry Resource for soft deleted images.
     *
     * @return void
     */
    public function test_soft_delete(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->trashed()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->unsetRelations();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function test_allowed_include_paths(): void
    {
        $schema = new EntrySchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        $schema = new EntrySchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                EntryResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function test_anime_by_media_format(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->unsetRelations()->load([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ]);

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function test_anime_by_season(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->unsetRelations()->load([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function test_anime_by_year(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
            )
            ->createOne();

        $entry->unsetRelations()->load([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function test_themes_by_sequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        AnimeTheme::ATTRIBUTE_SEQUENCE => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                    ])
            )
            ->createOne();

        $entry->unsetRelations()->load([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ]);

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall support constrained eager loading of themes by type.
     *
     * @return void
     */
    public function test_themes_by_type(): void
    {
        $typeFilter = Arr::random(ThemeType::cases());

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->unsetRelations()->load([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ]);

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryResource($entry, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
