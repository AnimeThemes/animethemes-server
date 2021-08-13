<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\Anime\ThemeType;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\Entry;
use App\Models\Wiki\Anime\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class EntryShowTest.
 */
class EntryShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Entry Show Endpoint shall return an Entry Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.entry.show', ['entry' => $entry]));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make())
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
    public function testSoftDelete()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        $entry->unsetRelations();

        $response = $this->get(route('api.entry.show', ['entry' => $entry]));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make())
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
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(EntryResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $entry = Entry::with($includedPaths->all())->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make($parameters))
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
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'version',
            'episodes',
            'nsfw',
            'spoiler',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                EntryResource::$wrap => $includedFields->join(','),
            ],
        ];

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make($parameters))
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
    public function testAnimeBySeason()
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $entry = Entry::with([
            'anime' => function (BelongsToThrough $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make($parameters))
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
    public function testAnimeByYear()
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'anime',
        ];

        Entry::factory()
            ->for(
                Theme::factory()->for(
                    Anime::factory()
                        ->state([
                            'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
            )
            ->create();

        $entry = Entry::with([
            'anime' => function (BelongsToThrough $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make($parameters))
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
    public function testThemesByGroup()
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::$param => [
                'group' => $groupFilter,
            ],
            IncludeParser::$param => 'theme',
        ];

        Entry::factory()
            ->for(
                Theme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'group' => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                    ])
            )
            ->create();

        $entry = Entry::with([
            'theme' => function (BelongsTo $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make($parameters))
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
    public function testThemesBySequence()
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'theme',
        ];

        Entry::factory()
            ->for(
                Theme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'sequence' => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                    ])
            )
            ->create();

        $entry = Entry::with([
            'theme' => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make($parameters))
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
    public function testThemesByType()
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
            IncludeParser::$param => 'theme',
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $entry = Entry::with([
            'theme' => function (BelongsTo $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
