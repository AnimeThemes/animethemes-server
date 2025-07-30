<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Features\AllowExternalProfileManagement;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()->createOne();

    $response = delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

    $response->assertForbidden();
});

test('forbidden if not own external profile', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()
        ->for(User::factory())
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::deactivate(AllowExternalProfileManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalProfile::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

    $response->assertForbidden();
});

test('deleted', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalProfile::class))->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

    $response->assertOk();
    $this->assertModelMissing($profile);
});

test('destroy permitted for bypass', function () {
    Event::fakeExcept(ExternalProfileCreated::class);

    Feature::activate(AllowExternalProfileManagement::class, fake()->boolean());

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(ExternalProfile::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    $profile = ExternalProfile::factory()
        ->for($user)
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

    $response->assertOk();
});
