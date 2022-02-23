<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Wiki\SongQuery;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SongShowTest.
 */
class SongShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Song Show Endpoint shall return a Song Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $this->withoutEvents();

        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall return a Song Resource for soft deleted songs.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $this->withoutEvents();

        $song = Song::factory()->createOne();

        $song->delete();

        $song->unsetRelations();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery())
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
    public function testAllowedIncludePaths(): void
    {
        $schema = new SongSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $song = Song::with($includedPaths->all())->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery($parameters))
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
    public function testSparseFieldsets(): void
    {
        $this->withoutEvents();

        $schema = new SongSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                SongResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery($parameters))
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
    public function testThemesByGroup(): void
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
            IncludeParser::param() => Song::RELATION_ANIMETHEMES,
        ];

        Song::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_GROUP => $groupFilter],
                        [AnimeTheme::ATTRIBUTE_GROUP => $excludedGroup],
                    ))
            )
            ->create();

        $song = Song::with([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery($parameters))
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
    public function testThemesBySequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => Song::RELATION_ANIMETHEMES,
        ];

        Song::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                    ))
            )
            ->create();

        $song = Song::with([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery($parameters))
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
    public function testThemesByType(): void
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
            ],
            IncludeParser::param() => Song::RELATION_ANIMETHEMES,
        ];

        Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->create();

        $song = Song::with([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery($parameters))
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
    public function testAnimeBySeason(): void
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::param() => Song::RELATION_ANIME,
        ];

        Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->create();

        $song = Song::with([
            Song::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery($parameters))
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
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Song::RELATION_ANIME,
        ];

        Song::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        Anime::factory()
                            ->state([
                                Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                            ])
                    )
            )
            ->create();

        $song = Song::with([
            Song::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->first();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SongResource::make($song, new SongQuery($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
