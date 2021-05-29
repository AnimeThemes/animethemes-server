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
                    EntryResource::make($entry, QueryParser::make())
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
        $allowedPaths = collect(EntryResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $entry = Entry::with($includedPaths->all())->first();

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
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
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
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $entry = Entry::with([
            'anime' => function ($query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
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
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
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
            'anime' => function ($query) use ($yearFilter) {
                $query->where('year', $yearFilter);
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
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $groupFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'theme',
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
            'theme' => function ($query) use ($groupFilter) {
                $query->where('group', $groupFilter);
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
        $sequenceFilter = $this->faker->randomDigitNotNull;
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequenceFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'theme',
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
            'theme' => function ($query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
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
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $typeFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'theme',
        ];

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $entry = Entry::with([
            'theme' => function ($query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
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
