<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Features\AllowExternalProfileManagement;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalProfileUpdateTest.
 */
class ExternalProfileUpdateTest extends TestCase
{
    use WithFaker;

    /**
     * The External Profile Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $profile = ExternalProfile::factory()->createOne();

        $visibility = Arr::random(ExternalProfileVisibility::cases());

        $parameters = array_merge(
            ExternalProfile::factory()->raw(),
            [ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->localize()],
        );

        $response = $this->put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The External Profile Update Endpoint shall forbid users without the update profile permission.
     *
     * @return void
     */
    public function test_forbidden_if_missing_permission(): void
    {
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

        $response = $this->put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Profile Update Endpoint shall forbid users from updating the profile if they don't own it.
     *
     * @return void
     */
    public function test_forbidden_if_not_own_external_profile(): void
    {
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

        $response = $this->put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Profile Update Endpoint shall forbid users from updating profiles
     * if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function test_forbidden_if_flag_disabled(): void
    {
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

        $response = $this->put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Profile Update Endpoint shall forbid users from updating a profile that is trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(ExternalProfile::class))->createOne();

        $profile = ExternalProfile::factory()
            ->trashed()
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

        $response = $this->put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The External Profile Update Endpoint shall update a profile.
     *
     * @return void
     */
    public function test_update(): void
    {
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

        $response = $this->put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

        $response->assertOk();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to update profiles
     * even if the Allow ExternalProfile Management feature is inactive.
     *
     * @return void
     */
    public function test_update_permitted_for_bypass(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        Feature::activate(AllowExternalProfileManagement::class, $this->faker->boolean());

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

        $response = $this->put(route('api.externalprofile.update', ['externalprofile' => $profile] + $parameters));

        $response->assertOk();
    }
}
