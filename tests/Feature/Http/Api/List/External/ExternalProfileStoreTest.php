<?php

declare(strict_types=1);

use App\Constants\Config\ExternalProfileConstants;
use App\Constants\Config\ValidationConstants;
use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Enums\Rules\ModerationService;
use App\Features\AllowExternalProfileManagement;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

uses(WithFaker::class);

test('protected', function (): void {
    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()->makeOne();

    $response = post(route('api.externalprofile.store', $profile->toArray()));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function (): void {
    Feature::activate(AllowExternalProfileManagement::class);

    $profile = ExternalProfile::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $profile->toArray()));

    $response->assertForbidden();
});

test('forbidden if flag disabled', function (): void {
    Feature::deactivate(AllowExternalProfileManagement::class);

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $parameters));

    $response->assertForbidden();
});

test('required fields', function (): void {
    Feature::activate(AllowExternalProfileManagement::class);

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store'));

    $response->assertJsonValidationErrors([
        ExternalProfile::ATTRIBUTE_NAME,
        ExternalProfile::ATTRIBUTE_SITE,
    ]);
});

test('create', function (): void {
    Feature::activate(AllowExternalProfileManagement::class);

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(ExternalProfile::class, 1);
    $this->assertDatabaseHas(ExternalProfile::class, [ExternalProfile::ATTRIBUTE_USER => $user->getKey()]);
});

test('create permitted for bypass', function (): void {
    Feature::activate(AllowExternalProfileManagement::class, fake()->boolean());

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(ExternalProfile::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $parameters));

    $response->assertCreated();
});

test('max profile limit', function (): void {
    $profileLimit = fake()->randomDigitNotNull();

    Config::set(ExternalProfileConstants::MAX_PROFILES_QUALIFIED, $profileLimit);
    Feature::activate(AllowExternalProfileManagement::class);

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()
        ->has(ExternalProfile::factory()->count($profileLimit))
        ->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $parameters));

    $response->assertForbidden();
});

test('max profile limit permitted for bypass', function (): void {
    $profileLimit = fake()->randomDigitNotNull();

    Config::set(ExternalProfileConstants::MAX_PROFILES_QUALIFIED, $profileLimit);
    Feature::activate(AllowExternalProfileManagement::class);

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()
        ->has(ExternalProfile::factory()->count($profileLimit))
        ->withPermissions(
            CrudPermission::CREATE->format(ExternalProfile::class),
            SpecialPermission::BYPASS_FEATURE_FLAGS->value
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $parameters));

    $response->assertCreated();
});

test('created if not flagged by open ai', function (): void {
    Feature::activate(AllowExternalProfileManagement::class);
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response([
            'results' => [
                0 => [
                    'flagged' => false,
                ],
            ],
        ]),
    ]);

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $parameters));

    $response->assertCreated();
});

test('created if open ai fails', function (): void {
    Feature::activate(AllowExternalProfileManagement::class);
    Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

    Http::fake([
        'https://api.openai.com/v1/moderations' => Http::response(status: 404),
    ]);

    $visibility = Arr::random(ExternalProfileVisibility::cases());

    $parameters = array_merge(
        ExternalProfile::factory()->raw(),
        [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.externalprofile.store', $parameters));

    $response->assertCreated();
});
