<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Resources\List\Resource\ExternalProfileResource;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalProfileShowTest.
 */
class ExternalProfileShowTest extends TestCase
{
    use WithFaker;

    /**
     * The External Profile Show Endpoint shall forbid a private profile from being publicly viewed.
     *
     * @return void
     */
    public function testPrivateExternalProfileCannotBePubliclyViewed(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Show Endpoint shall forbid the user from viewing a private profile if not owned.
     *
     * @return void
     */
    public function testPrivateExternalProfileCannotBePubliclyIfNotOwned(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalProfile::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile]));

        $response->assertForbidden();
    }

    /**
     * The External Profile Show Endpoint shall allow a private profile to be viewed by the owner.
     *
     * @return void
     */
    public function testPrivateExternalProfileCanBeViewedByOwner(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalProfile::class))->createOne();

        $profile = ExternalProfile::factory()
            ->for($user)
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

        Sanctum::actingAs($user);

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile]));

        $response->assertOk();
    }

    /**
     * The External Profile Show Endpoint shall allow a public profile to be viewed.
     *
     * @return void
     */
    public function testPublicExternalProfileCanBeViewed(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile]));

        $response->assertOk();
    }

    /**
     * By default, the External Profile Show Endpoint shall return a External Profile Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $profile = ExternalProfile::factory()
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileResource($profile, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Show Endpoint shall return a External Profile Resource for soft deleted profiles.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $profile = ExternalProfile::factory()
            ->trashed()
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $profile->unsetRelations();

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileResource($profile, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $schema = new ExternalProfileSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

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

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileResource($profile, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The External Profile Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        Event::fakeExcept(ExternalProfileCreated::class);

        $schema = new ExternalProfileSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ExternalProfileResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $profile = ExternalProfile::factory()
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $response = $this->get(route('api.externalprofile.show', ['externalprofile' => $profile] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new ExternalProfileResource($profile, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
