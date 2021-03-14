<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\InvitationStatus;
use App\Enums\UserRole;
use App\Nova\Actions\ResendInvitationAction;
use App\Nova\Filters\InvitationStatusFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Filters\UserRoleFilter;
use App\Nova\Invitation;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use NovaResourceTest, RefreshDatabase, WithFaker;

    /**
     * The Invitation Resource shall contain Invitation Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Invitation::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.name'));
        $resource->assertHasField(__('nova.email'));
        $resource->assertHasField(__('nova.role'));
        $resource->assertHasField(__('nova.status'));
    }

    /**
     * The Invitation Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Invitation Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Invitation Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Invitation Resource shall contain a Deleted At field.
     *
     * @return void
     */
    public function testDeletedAtField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Invitation Resource shall contain a Name field.
     *
     * @return void
     */
    public function testNameField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.name'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Invitation Resource shall contain a Email field.
     *
     * @return void
     */
    public function testEmailField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.email'));

        $field->assertHasRule('required');
        $field->assertHasRule('email');
        $field->assertHasRule('max:192');
        $field->assertHasCreationRule('unique:invitation,email');
        $field->assertHasUpdateRule('unique:invitation,email,{{resourceId}},invitation_id');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Invitation Resource shall contain a Role field.
     *
     * @return void
     */
    public function testRoleField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.role'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(UserRole::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Invitation Resource shall contain a Status field.
     *
     * @return void
     */
    public function testStatusField()
    {
        $resource = $this->novaResource(Invitation::class);

        $field = $resource->field(__('nova.status'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(InvitationStatus::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Invitation Resource shall contain Invitation Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Invitation::class);

        $resource->assertHasFilter(UserRoleFilter::class);
        $resource->assertHasFilter(InvitationStatusFilter::class);
        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Invitation Resource shall contain Invitation Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Invitation::class);

        $resource->assertHasAction(ResendInvitationAction::class);
    }
}
