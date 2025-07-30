<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Features\AllowExternalProfileManagement;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()->createOne();

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $response = put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()->createOne();

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

    $response->assertForbidden();
});

test('forbidden if not own external profile', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::deactivate(AllowExternalProfileManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(ExternalProfile::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne();

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [
            ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize(),
        ],
    );

    Sanctum::actingAs($user);

    $response = put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(ExternalProfile::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne();

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [
            ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize(),
        ],
    );

    Sanctum::actingAs($user);

    $response = put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

    $response->assertOk();
});

test('update permitted for bypass', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class, fake()->boolean());

    $user = User::factory()
        ->withPermissions(
            CrudPermission::UPDATE->format(ExternalProfile::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne();

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [
            ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize(),
        ],
    );

    Sanctum::actingAs($user);

    $response = put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

    $response->assertOk();
});
