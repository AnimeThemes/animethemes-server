<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistSong;

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistSongSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ArtistSongShowTest.
 */
class ArtistSongShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Artist Song Show Endpoint shall return an error if the artist song does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $artist = Artist::factory()->createOne();
        $song = Song::factory()->createOne();

        $response = $this->get(route('api.artistsong.show', ['artist' => $artist, 'song' => $song]));

        $response->assertNotFound();
    }

    /**
     * By default, the Artist Song Show Endpoint shall return an Artist Song Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $response = $this->get(route('api.artistsong.show', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

        $artistSong->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongResource($artistSong, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Song Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ArtistSongSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $response = $this->get(route('api.artistsong.show', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

        $artistSong->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongResource($artistSong, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Song Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ArtistSongSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistSongResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $artistSong = ArtistSong::factory()
            ->for(Artist::factory())
            ->for(Song::factory())
            ->createOne();

        $response = $this->get(route('api.artistsong.show', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

        $artistSong->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongResource($artistSong, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
