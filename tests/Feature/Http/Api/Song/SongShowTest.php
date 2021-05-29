<?php

namespace Tests\Feature\Http\Api\Song;

use App\Enums\AnimeSeason;
use App\Enums\ThemeType;
use App\Http\Resources\SongResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Song;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SongShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Song Show Endpoint shall return a Song Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall return an Song Song for soft deleted songs.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $this->withoutEvents();

        $song = Song::factory()->createOne();

        $song->delete();

        $song->unsetRelations();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(SongResource::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $song = Song::with($includedPaths->all())->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'title',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SongResource::$wrap => $includedFields->join(','),
            ],
        ];

        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall support constrained eager loading of themes by group.
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
            QueryParser::PARAM_INCLUDE => 'themes',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['group' => $groupFilter],
                        ['group' => $excludedGroup],
                    ))
            )
            ->create();

        $song = Song::with([
            'themes' => function ($query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall support constrained eager loading of themes by sequence.
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
            QueryParser::PARAM_INCLUDE => 'themes',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['sequence' => $sequenceFilter],
                        ['sequence' => $excludedSequence],
                    ))
            )
            ->create();

        $song = Song::with([
            'themes' => function ($query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall support constrained eager loading of themes by type.
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
            QueryParser::PARAM_INCLUDE => 'themes',
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->create();

        $song = Song::with([
            'themes' => function ($query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall support constrained eager loading of anime by season.
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
            QueryParser::PARAM_INCLUDE => 'themes.anime',
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->create();

        $song = Song::with([
            'themes.anime' => function ($query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall support constrained eager loading of anime by year.
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
            QueryParser::PARAM_INCLUDE => 'themes.anime',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Anime::factory()
                            ->state([
                                'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                            ])
                    )
            )
            ->create();

        $song = Song::with([
            'themes.anime' => function ($query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
