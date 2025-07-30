<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\ImageFacet;
use App\Events\List\Playlist\PlaylistCreated;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('private playlist cannot be publicly viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $response = get(route('api.playlist.show', ['playlist' => $playlist]));

    $response->assertForbidden();
});

test('private playlist cannot be publicly if not owned', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.playlist.show', ['playlist' => $playlist]));

    $response->assertForbidden();
});

test('private playlist can be viewed by owner', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
        ]);

    Sanctum::actingAs($user);

    $response = get(route('api.playlist.show', ['playlist' => $playlist]));

    $response->assertOk();
});

test('unlisted playlist can be viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
        ]);

    $response = get(route('api.playlist.show', ['playlist' => $playlist]));

    $response->assertOk();
});

test('public playlist can be viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.playlist.show', ['playlist' => $playlist]));

    $response->assertOk();
});

test('default', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.playlist.show', ['playlist' => $playlist]));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistResource($playlist, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $schema = new PlaylistSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->has(PlaylistTrack::factory(), Playlist::RELATION_FIRST)
        ->has(PlaylistTrack::factory(), Playlist::RELATION_LAST)
        ->has(PlaylistTrack::factory()->count(fake()->randomDigitNotNull()), Playlist::RELATION_TRACKS)
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistResource($playlist, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $schema = new PlaylistSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            PlaylistResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistResource($playlist, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('images by facet', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $facetFilter = Arr::random(ImageFacet::cases());

    $parameters = [
        FilterParser::param() => [
            Image::ATTRIBUTE_FACET => $facetFilter->localize(),
        ],
        IncludeParser::param() => Playlist::RELATION_IMAGES,
    ];

    $playlist = Playlist::factory()
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $playlist->unsetRelations()->load([
        Playlist::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response = get(route('api.playlist.show', ['playlist' => $playlist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistResource($playlist, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
