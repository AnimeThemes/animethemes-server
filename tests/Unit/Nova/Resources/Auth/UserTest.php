<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Resources\Auth;

use App\Models\Auth\User as UserModel;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Resources\Auth\User;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Validation\Rule;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    use NovaResourceTest;
    use WithoutEvents;

    /**
     * The User Resource shall contain User Fields.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFields()
    {
        $resource = static::novaResource(User::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.name'));
        $resource->assertHasField(__('nova.email'));
    }

    /**
     * The User Resource shall contain an ID field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField()
    {
        $resource = static::novaResource(User::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The User Resource shall contain a Created At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField()
    {
        $resource = static::novaResource(User::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The User Resource shall contain an Updated At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField()
    {
        $resource = static::novaResource(User::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The User Resource shall contain a Deleted At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField()
    {
        $resource = static::novaResource(User::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The User Resource shall contain a Name field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testNameField()
    {
        $resource = static::novaResource(User::class);

        $field = $resource->field(__('nova.name'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('alpha_dash');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The User Resource shall contain a Email field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testEmailField()
    {
        $resource = static::novaResource(User::class);

        $field = $resource->field(__('nova.email'));

        $field->assertHasRule('required');
        $field->assertHasRule('email');
        $field->assertHasRule('max:192');
        $field->assertHasCreationRule(Rule::unique(UserModel::TABLE)->__toString());
        $field->assertHasUpdateRule(Rule::unique(UserModel::TABLE)->ignore(null, UserModel::ATTRIBUTE_ID)->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The User Resource shall contain User Filters.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFilters()
    {
        $resource = static::novaResource(User::class);

        $resource->assertHasFilter(CreatedStartDateFilter::class);
        $resource->assertHasFilter(CreatedEndDateFilter::class);
        $resource->assertHasFilter(UpdatedStartDateFilter::class);
        $resource->assertHasFilter(UpdatedEndDateFilter::class);
        $resource->assertHasFilter(DeletedStartDateFilter::class);
        $resource->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The User Resource shall contain User Actions.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testActions()
    {
        $resource = static::novaResource(User::class);

        $resource->assertHasNoActions();
    }
}
