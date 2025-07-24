<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External;

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
use Tests\TestCase;

class ExternalProfileStoreTest extends TestCase
{
    use WithFaker;

    /**
     * The External Profile Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->makeOne();

        $response = $this->post(route('api.externalprofile.store', $profile->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The External Profile Store Endpoint shall forbid users without the create profile permission.
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.externalprofile.store', $profile->toArray()));

        $response->assertForbidden();
    }

    /**
     * The External Profile Store Endpoint shall forbid users from creating profiles
     * if the Allow ExternalProfile Management feature is inactive.
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Feature::deactivate(AllowExternalProfileManagement::class);

        $visibility = Arr::random(ExternalProfileVisibility::cases());

        $parameters = array_merge(
            ExternalProfile::factory()->raw(),
            [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Profile Store Endpoint shall require name & site fields.
     */
    public function testRequiredFields(): void
    {
        Feature::activate(AllowExternalProfileManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.externalprofile.store'));

        $response->assertJsonValidationErrors([
            ExternalProfile::ATTRIBUTE_NAME,
            ExternalProfile::ATTRIBUTE_SITE,
        ]);
    }

    /**
     * The External Profile Store Endpoint shall create a profile.
     */
    public function testCreate(): void
    {
        Feature::activate(AllowExternalProfileManagement::class);

        $visibility = Arr::random(ExternalProfileVisibility::cases());

        $parameters = array_merge(
            ExternalProfile::factory()->raw(),
            [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(ExternalProfile::class, 1);
        static::assertDatabaseHas(ExternalProfile::class, [ExternalProfile::ATTRIBUTE_USER => $user->getKey()]);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to create profiles
     * even if the Allow ExternalProfile Management feature is inactive.
     */
    public function testCreatePermittedForBypass(): void
    {
        Feature::activate(AllowExternalProfileManagement::class, $this->faker->boolean());

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

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The External Profile Store Endpoint shall forbid users from creating profiles that exceed the user profile limit.
     */
    public function testMaxProfileLimit(): void
    {
        $profileLimit = $this->faker->randomDigitNotNull();

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

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Profile Store Endpoint shall permit users with bypass feature flag permission
     * to create profiles that exceed the user profile limit.
     */
    public function testMaxProfileLimitPermittedForBypass(): void
    {
        $profileLimit = $this->faker->randomDigitNotNull();

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

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The ExternalProfile Store Endpoint shall create a profile if the name is not flagged by OpenAI.
     */
    public function testCreatedIfNotFlaggedByOpenAi(): void
    {
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

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The External Profile Store Endpoint shall create a profile if the moderation service returns some error.
     */
    public function testCreatedIfOpenAiFails(): void
    {
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

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertCreated();
    }

    /**
     * The External Profile Store Endpoint shall prohibit users from creating profiles with names flagged by OpenAI.
     */
    public function testValidationErrorWhenFlaggedByOpenAi(): void
    {
        Feature::activate(AllowExternalProfileManagement::class);
        Config::set(ValidationConstants::MODERATION_SERVICE_QUALIFIED, ModerationService::OPENAI->value);

        Http::fake([
            'https://api.openai.com/v1/moderations' => Http::response([
                'results' => [
                    0 => [
                        'flagged' => true,
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

        $response = $this->post(route('api.externalprofile.store', $parameters));

        $response->assertJsonValidationErrors([
            ExternalProfile::ATTRIBUTE_NAME,
        ]);
    }
}
