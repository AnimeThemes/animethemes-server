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
use App\Http\Api\Query\Wiki\Anime\Theme\EntryQuery;
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
                    EntryResource::make($entry, EntryQuery::make())
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
                    EntryResource::make($entry, EntryQuery::make())
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $entry = AnimeThemeEntry::with($includedPaths->all())->first();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, EntryQuery::make($parameters))
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
                    EntryResource::make($entry, EntryQuery::make($parameters))
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $entry = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, EntryQuery::make($parameters))
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

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
            )
            ->create();

        $entry = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, EntryQuery::make($parameters))
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

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        AnimeTheme::ATTRIBUTE_GROUP => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                    ])
            )
            ->create();

        $entry = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, EntryQuery::make($parameters))
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

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        AnimeTheme::ATTRIBUTE_SEQUENCE => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                    ])
            )
            ->create();

        $entry = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, EntryQuery::make($parameters))
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $entry = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.animethemeentry.show', ['animethemeentry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, EntryQuery::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
