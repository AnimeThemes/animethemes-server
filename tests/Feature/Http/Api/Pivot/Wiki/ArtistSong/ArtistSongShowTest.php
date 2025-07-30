<?php

declare(strict_types=1);

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

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $response = get(route('api.artistsong.show', ['artist' => $artist, 'song' => $song]));

    $response->assertNotFound();
});

test('default', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $response = get(route('api.artistsong.show', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

    $artistSong->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistSongResource($artistSong, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ArtistSongSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $response = get(route('api.artistsong.show', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

    $artistSong->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistSongResource($artistSong, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ArtistSongSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ArtistSongResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $response = get(route('api.artistsong.show', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

    $artistSong->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistSongResource($artistSong, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
