<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\List\External\Entry;

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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ExternalEntryShowTest.
 */
class ExternalEntryShowTest extends TestCase
{
    use WithFaker;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Event::fakeExcept(ExternalProfileCreated::class);
    }

    /**
     * The External Entry Show Endpoint shall forbid a private profile from being publicly viewed.
     *
     * @return void
     */
    public function test_private_external_entry_cannot_be_publicly_viewed(): void
    {
        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value,
            ]);

        $entry = ExternalEntry::factory()
            ->for($profile)
            ->createOne();

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

        $response->assertForbidden();
    }

    /**
     * The External Entry Show Endpoint shall forbid the user from viewing a private profile entry if not owned.
     *
     * @return void
     */
    public function test_private_external_entry_cannot_be_publicly_viewed_if_not_owned(): void
    {
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

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

        $response->assertForbidden();
    }

    /**
     * The External Entry Show Endpoint shall allow a private profile entry to be viewed by the owner.
     *
     * @return void
     */
    public function test_private_external_entry_can_be_viewed_by_owner(): void
    {
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

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

        $response->assertOk();
    }

    /**
     * The External Entry Show Endpoint shall allow a public profile entry to be viewed.
     *
     * @return void
     */
    public function test_public_external_entry_can_be_viewed(): void
    {
        $profile = ExternalProfile::factory()
            ->for(User::factory())
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $entry = ExternalEntry::factory()
            ->for($profile)
            ->createOne();

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

        $response->assertOk();
    }

    /**
     * The External Entry Show Endpoint shall scope bindings.
     *
     * @return void
     */
    public function test_scoped(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalEntry::class))->createOne();

        $profile = ExternalProfile::factory()
            ->for($user)
            ->has(ExternalEntry::factory()->count($this->faker->randomDigitNotNull()), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory()->for(User::factory()))
            ->createOne();

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

        $response->assertNotFound();
    }

    /**
     * By default, the External Entry Show Endpoint shall return an External Entry Resource.
     *
     * @return void
     */
    public function test_default(): void
    {
        $profile = ExternalProfile::factory()
            ->createOne([
                ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
            ]);

        $entry = ExternalEntry::factory()
            ->for($profile)
            ->createOne();

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry]));

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
    }

    /**
     * The External Entry Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function test_allowed_include_paths(): void
    {
        $schema = new ExternalEntrySchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

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

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry] + $parameters));

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
    }

    /**
     * The External Entry Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        $schema = new ExternalEntrySchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

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

        $response = $this->get(route('api.externalprofile.externalentry.show', ['externalprofile' => $profile, 'externalentry' => $entry] + $parameters));

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
    }
}
