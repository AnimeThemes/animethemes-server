<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Features\AllowExternalProfileManagement;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalProfileDestroyTest.
 */
class ExternalProfileDestroyTest extends TestCase
{
    use WithFaker;

    /**
     * The External Profile Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $response = $this->delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

        $response->assertUnauthorized();
    }

    /**
     * The External Profile Destroy Endpoint shall forbid users without the delete profile permission.
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

        $response = $this->delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Destroy Endpoint shall forbid users from deleting the profile if they don't own it.
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

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Destroy Endpoint shall forbid users from destroying profiles
     * if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function testForbiddenIfFlagDisabled(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::deactivate(AllowExternalProfileManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalProfile::class))->createOne();

        $profile = ExternalProfile::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Destroy Endpoint shall delete the profile.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(ExternalProfile::class))->createOne();

        $profile = ExternalProfile::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

        $response->assertOk();
        static::assertSoftDeleted($profile);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to destroy profiles
     * even if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function testDestroyPermittedForBypass(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class, $this->faker->boolean());

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

        $response = $this->delete(route('api.externalprofile.destroy', ['externalprofile' => $profile]));

        $response->assertOk();
    }
}
