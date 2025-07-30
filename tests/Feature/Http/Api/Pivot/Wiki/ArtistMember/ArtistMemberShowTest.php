<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistMemberSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $member = Artist::factory()->createOne();

    $response = $this->get(route('api.artistmember.show', ['artist' => $artist, 'member' => $member]));

    $response->assertNotFound();
});

test('default', function () {
    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $response = $this->get(route('api.artistmember.show', ['artist' => $artistMember->artist, 'member' => $artistMember->member]));

    $artistMember->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberResource($artistMember, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ArtistMemberSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $response = $this->get(route('api.artistmember.show', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

    $artistMember->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberResource($artistMember, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ArtistMemberSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ArtistMemberResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $artistMember = ArtistMember::factory()
        ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
        ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
        ->createOne();

    $response = $this->get(route('api.artistmember.show', ['artist' => $artistMember->artist, 'member' => $artistMember->member] + $parameters));

    $artistMember->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberResource($artistMember, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
