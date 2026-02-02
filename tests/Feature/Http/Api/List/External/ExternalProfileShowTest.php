<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Resources\List\Resource\ExternalProfileJsonResource;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('private external profile cannot be publicly viewed', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    $response = get(route('api.externalprofile.show', ['externalprofile' => $profile]));

    $response->assertForbidden();
});

test('private external profile cannot be publicly if not owned', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.externalprofile.show', ['externalprofile' => $profile]));

    $response->assertForbidden();
});

test('private external profile can be viewed by owner', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalProfile::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    Sanctum::actingAs($user);

    $response = get(route('api.externalprofile.show', ['externalprofile' => $profile]));

    $response->assertOk();
});

test('public external profile can be viewed', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.externalprofile.show', ['externalprofile' => $profile]));

    $response->assertOk();
});

test('default', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.externalprofile.show', ['externalprofile' => $profile]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileJsonResource($profile, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    $schema = new ExternalProfileSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->has(ExternalEntry::factory(), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.externalprofile.show', ['externalprofile' => $profile] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileJsonResource($profile, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    $schema = new ExternalProfileSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ExternalProfileJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $response = get(route('api.externalprofile.show', ['externalprofile' => $profile] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileJsonResource($profile, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
