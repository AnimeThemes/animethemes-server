<?php

namespace Tests\Feature\Http\Api\Entry;

use App\Enums\AnimeSeason;
use App\Enums\ThemeType;
use App\Http\Resources\EntryResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Entry Show Endpoint shall return an Entry Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $entry = Entry::with(EntryResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry]));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make())
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
        $allowed_paths = collect(EntryResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $entry = Entry::with($included_paths->all())->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make($parameters))
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
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                EntryResource::$resourceType => $included_fields->join(','),
            ],
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $entry = Entry::with(EntryResource::allowedIncludePaths())->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make($parameters))
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
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $entry = Entry::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make($parameters))
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
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Entry::factory()
            ->for(
                Theme::factory()->for(
                    Anime::factory()
                        ->state([
                            'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                        ])
                )
            )
            ->create();

        $entry = Entry::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make($parameters))
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
        $group_filter = $this->faker->word();
        $excluded_group = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $group_filter,
            ],
        ];

        Entry::factory()
            ->for(
                Theme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'group' => $this->faker->boolean() ? $group_filter : $excluded_group,
                    ])
            )
            ->create();

        $entry = Entry::with([
            'theme' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make($parameters))
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
        $sequence_filter = $this->faker->randomDigitNotNull;
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
        ];

        Entry::factory()
            ->for(
                Theme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'sequence' => $this->faker->boolean() ? $sequence_filter : $excluded_sequence,
                    ])
            )
            ->create();

        $entry = Entry::with([
            'theme' => function ($query) use ($sequence_filter) {
                $query->where('sequence', $sequence_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make($parameters))
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
        $type_filter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $type_filter->key,
            ],
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $entry = Entry::with([
            'theme' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.entry.show', ['entry' => $entry] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryResource::make($entry, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
