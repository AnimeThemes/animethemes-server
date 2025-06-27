<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External;

use App\Enums\Auth\ExtendedCrudPermission;
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
 * Class ExternalProfileForceDeleteTest.
 */
class ExternalProfileForceDeleteTest extends TestCase
{
    use WithFaker;

    /**
     * The External Profile Force Delete Endpoint shall require authorization.
     *
     * @return void
     */
    public function test_authorized(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $response = $this->delete(route('api.externalprofile.forceDelete', ['externalprofile' => $profile]));

        $response->assertUnauthorized();
    }

    /**
     * The External Profile Force Delete Endpoint shall forbid users without the force delete profile permission.
     *
     * @return void
     */
    public function test_forbidden_if_missing_permission(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.externalprofile.forceDelete', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Force Delete Endpoint shall forbid users from force deleting profiles
     * if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function test_forbidden_if_flag_disabled(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::deactivate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.externalprofile.forceDelete', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Force Delete Endpoint shall force delete the profile.
     *
     * @return void
     */
    public function test_deleted(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.externalprofile.forceDelete', ['externalprofile' => $profile]));

        $response->assertOk();
        static::assertModelMissing($profile);
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to force delete profiles
     * even if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function test_delete_permitted_for_bypass(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class, $this->faker->boolean());

        $user = User::factory()
            ->withPermissions(
                ExtendedCrudPermission::FORCE_DELETE->format(ExternalProfile::class),
                SpecialPermission::BYPASS_FEATURE_FLAGS->value
            )
            ->createOne();

        $profile = ExternalProfile::factory()
            ->for($user)
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.externalprofile.forceDelete', ['externalprofile' => $profile]));

        $response->assertOk();
    }
}
