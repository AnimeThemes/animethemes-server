<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External;

use App\Constants\Config\ExternalProfileConstants;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Features\AllowExternalProfileManagement;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalProfileRestoreTest.
 */
class ExternalProfileRestoreTest extends TestCase
{
    use WithFaker;

    /**
     * The External Profile Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertUnauthorized();
    }

    /**
     * The External Profile Restore Endpoint shall forbid users without the restore profile permission.
     *
     * @return void
     */
    public function testForbiddenIfMissingPermission(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Restore Endpoint shall forbid users from restoring the profile if they don't own it.
     *
     * @return void
     */
    public function testForbiddenIfNotOwnExternalProfile(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Restore Endpoint shall forbid users from restoring profiles
     * if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::deactivate(AllowExternalProfileManagement::class);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class))->createOne();

        $profile = ExternalProfile::factory()
            ->trashed()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Restore Endpoint shall forbid users from restoring a profile that isn't trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class))->createOne();

        $profile = ExternalProfile::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Restore Endpoint shall restore the profile.
     *
     * @return void
     */
    public function testRestored(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class))->createOne();

        $profile = ExternalProfile::factory()
            ->trashed()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertOk();
        static::assertNotSoftDeleted($profile);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to restore profiles
     * even if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function testCreatePermittedForBypass(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                ExtendedCrudPermission::RESTORE->format(ExternalProfile::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $profile = ExternalProfile::factory()
            ->trashed()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertOk();
        static::assertNotSoftDeleted($profile);
    }

    /**
     * The External Profile Restore Endpoint shall forbid users from restoring profiles that exceed the user profile limit.
     *
     * @return void
     */
    public function testMaxProfileLimit(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $profileLimit = $this->faker->randomDigitNotNull();

        Config::set(ExternalProfileConstants::MAX_PROFILES_QUALIFIED, $profileLimit);
        Feature::activate(AllowExternalProfileManagement::class);

        $user = User::factory()
            ->has(ExternalProfile::factory()->count($profileLimit))
            ->withPermissions(ExtendedCrudPermission::RESTORE->format(ExternalProfile::class))
            ->createOne();

        $profile = ExternalProfile::factory()
            ->trashed()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Restore Endpoint shall permit users with bypass feature flag permission
     * to restore profiles that exceed the user profile limit.
     *
     * @return void
     */
    public function testMaxProfileLimitPermittedForBypass(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $profileLimit = $this->faker->randomDigitNotNull();

        Config::set(ExternalProfileConstants::MAX_PROFILES_QUALIFIED, $profileLimit);
        Feature::activate(AllowExternalProfileManagement::class);

        $user = User::factory()
            ->has(ExternalProfile::factory()->count($profileLimit))
            ->withPermissions(
                ExtendedCrudPermission::RESTORE->format(ExternalProfile::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $profile = ExternalProfile::factory()
            ->trashed()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.externalprofile.restore', ['externalprofile' => $profile]));

        $response->assertOk();
    }
}
