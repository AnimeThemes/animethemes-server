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
use App\Http\Api\Schema\List\External\ExternalEntrySchema;
use App\Http\Resources\List\External\Resource\ExternalEntryResource;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

/**
 * Setup the test environment.
 */
beforeEach(function () {
    Event::fakeExcept(ExternalProfileCreated::class);
});

test('private external entry cannot be publicly viewed', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for($profile)
        ->createOne();

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

    $response->assertForbidden();
});

test('private external entry cannot be publicly viewed if not owned', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for($profile)
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

    $response->assertForbidden();
});

test('private external entry can be viewed by owner', function () {
    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for($profile)
        ->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

    $response->assertOk();
});

test('public external entry can be viewed', function () {
    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for($profile)
        ->createOne();

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

    $response->assertOk();
});

test('scoped', function () {
    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->has(ExternalEntry::factory()->count(fake()->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for(ExternalProfile::factory()->for(User::factory()))
        ->createOne();

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

    $response->assertNotFound();
});

test('default', function () {
    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for($profile)
        ->createOne();

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

    $entry->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryResource($entry, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ExternalEntrySchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for($profile)
        ->for(Anime::factory())
        ->createOne();

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry] + $parameters));

    $entry->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ExternalEntrySchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ExternalEntryResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $profile = ExternalProfile::factory()
        ->createOne([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $entry = ExternalEntry::factory()
        ->for($profile)
        ->createOne();

    $response = get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry] + $parameters));

    $entry->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalEntryResource($entry, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
