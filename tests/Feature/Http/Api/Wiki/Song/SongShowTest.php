<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Song;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
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
use Illuminate\Support\Arr;
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
        $song = Song::factory()->create();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query())
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
        $song = Song::factory()->trashed()->createOne();

        $song->unsetRelations();

        $response = $this->get(route('api.song.show', ['song' => $song]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query())
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

        $song = Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull()))
            ->createOne();

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query($parameters))
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
                    new SongResource($song, new Query($parameters))
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

        $song = Song::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Anime::factory())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                    ))
            )
            ->createOne();

        $song->unsetRelations()->load([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ]);

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query($parameters))
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
        $typeFilter = Arr::random(ThemeType::cases());

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
            IncludeParser::param() => Song::RELATION_ANIMETHEMES,
        ];

        $song = Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->createOne();

        $song->unsetRelations()->load([
            Song::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ]);

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Song Show Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function testAnimeByMediaFormat(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => Song::RELATION_ANIME,
        ];

        $song = Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->createOne();

        $song->unsetRelations()->load([
            Song::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ]);

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query($parameters))
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
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => Song::RELATION_ANIME,
        ];

        $song = Song::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull())->for(Anime::factory()))
            ->createOne();

        $song->unsetRelations()->load([
            Song::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query($parameters))
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

        $song = Song::factory()
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
            ->createOne();

        $song->unsetRelations()->load([
            Song::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response = $this->get(route('api.song.show', ['song' => $song] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new SongResource($song, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
