<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\Anime\ThemeType;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Anime\Theme;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SongShowTest.
 */
class SongShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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
                    SongResource::make($song, Query::make())
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
                    SongResource::make($song, Query::make())
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
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $song = Song::with($includedPaths->all())->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, Query::make($parameters))
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
            FieldParser::$param => [
                SongResource::$wrap => $includedFields->join(','),
            ],
        ];

        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, Query::make($parameters))
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
            FilterParser::$param => [
                'group' => $groupFilter,
            ],
            IncludeParser::$param => 'themes',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['group' => $groupFilter],
                        ['group' => $excludedGroup],
                    ))
            )
            ->create();

        $song = Song::with([
            'themes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, Query::make($parameters))
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
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'themes',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        ['sequence' => $sequenceFilter],
                        ['sequence' => $excludedSequence],
                    ))
            )
            ->create();

        $song = Song::with([
            'themes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, Query::make($parameters))
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
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
            IncludeParser::$param => 'themes',
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->create();

        $song = Song::with([
            'themes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, Query::make($parameters))
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
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'themes.anime',
        ];

        Song::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->create();

        $song = Song::with([
            'themes.anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, Query::make($parameters))
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
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'themes.anime',
        ];

        Song::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Anime::factory()
                            ->state([
                                'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                            ])
                    )
            )
            ->create();

        $song = Song::with([
            'themes.anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
