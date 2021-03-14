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
     * By default, the Song Show Endpoint shall return a Song Resource with all allowed include paths.
     *
     * @return void
     */
    public function testDefault()
    {
        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $song = Song::with(SongResource::allowedIncludePaths())->first();

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
        $song = Song::factory()->createOne();

        $song->delete();

        $song = Song::withTrashed()->with(SongResource::allowedIncludePaths())->first();

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
        $allowed_paths = collect(SongResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $song = Song::with($included_paths->all())->first();

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
        $fields = collect([
            'id',
            'title',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SongResource::$wrap => $included_fields->join(','),
            ],
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $song = Song::with(SongResource::allowedIncludePaths())->first();

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
        $group_filter = $this->faker->word();
        $excluded_group = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $group_filter,
            ],
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['group' => $group_filter],
                        ['group' => $excluded_group],
                    ))
            )
            ->create();

        $song = Song::with([
            'themes' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
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
        $sequence_filter = $this->faker->randomDigitNotNull;
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['sequence' => $sequence_filter],
                        ['sequence' => $excluded_sequence],
                    ))
            )
            ->create();

        $song = Song::with([
            'themes' => function ($query) use ($sequence_filter) {
                $query->where('sequence', $sequence_filter);
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
        $type_filter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $type_filter->key,
            ],
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->create();

        $song = Song::with([
            'themes' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
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
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull)->for(Anime::factory()))
            ->create();

        $song = Song::with([
            'themes.anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
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
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Anime::factory()
                            ->state([
                                'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                            ])
                    )
            )
            ->create();

        $song = Song::with([
            'themes.anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
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
