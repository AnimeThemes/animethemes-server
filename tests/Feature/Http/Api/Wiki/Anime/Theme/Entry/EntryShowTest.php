<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Wiki\Anime\Theme\Entry\EntryReadQuery;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function testDefault(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new EntryResource($entry, new EntryReadQuery()))
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
    public function testSoftDelete(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        $entry->unsetRelations();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new EntryResource($entry, new EntryReadQuery()))
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
    public function testAllowedIncludePaths(): void
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
                    (new EntryResource($entry, new EntryReadQuery($parameters)))
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
    public function testSparseFieldsets(): void
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
                    (new EntryResource($entry, new EntryReadQuery($parameters)))
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
    public function testAnimeBySeason(): void
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
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
                    (new EntryResource($entry, new EntryReadQuery($parameters)))
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
    public function testAnimeByYear(): void
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
                    (new EntryResource($entry, new EntryReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Show Endpoint shall support constrained eager loading of themes by group.
     *
     * @return void
     */
    public function testThemesByGroup(): void
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
        ];

        $entry = AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        AnimeTheme::ATTRIBUTE_GROUP => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                    ])
            )
            ->createOne();

        $entry->unsetRelations()->load([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ]);

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new EntryResource($entry, new EntryReadQuery($parameters)))
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
    public function testThemesBySequence(): void
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
                    (new EntryResource($entry, new EntryReadQuery($parameters)))
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
    public function testThemesByType(): void
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
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
                    (new EntryResource($entry, new EntryReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
