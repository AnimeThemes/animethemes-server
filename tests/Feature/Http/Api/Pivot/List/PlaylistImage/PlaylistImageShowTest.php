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
use App\Http\Api\Schema\Pivot\List\PlaylistImageSchema;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);
    $image = Image::factory()->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlist, 'image' => $image]));

    $response->assertNotFound();
});

test('private playlist image cannot be publicly viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertForbidden();
});

test('private playlist image cannot be publicly if not owned', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertForbidden();
});

test('private playlist image can be viewed by owner', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for($user)
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertOk();
});

test('unlisted playlist image can be viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertOk();
});

test('public playlist can be viewed', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $response->assertOk();
});

test('default', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image]));

    $playlistImage->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageResource($playlistImage, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $schema = new PlaylistImageSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image] + $parameters));

    $playlistImage->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageResource($playlistImage, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    Event::fakeExcept(PlaylistCreated::class);

    $schema = new PlaylistImageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            PlaylistImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image] + $parameters));

    $playlistImage->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageResource($playlistImage, new Query($parameters))
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
        IncludeParser::param() => PlaylistImage::RELATION_IMAGE,
    ];

    $playlistImage = PlaylistImage::factory()
        ->for(
            Playlist::factory()
                ->for(User::factory())
                ->state([
                    Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = get(route('api.playlistimage.show', ['playlist' => $playlistImage->playlist, 'image' => $playlistImage->image] + $parameters));

    $playlistImage->unsetRelations()->load([
        PlaylistImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistImageResource($playlistImage, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
