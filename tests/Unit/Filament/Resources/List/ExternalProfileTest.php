<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Resources\List\ExternalProfile;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile as ExternalProfileModel;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class ExternalProfileTest.
 */
class ExternalProfileTest extends BaseResourceTestCase
{
    /**
     * Initial setup for the tests.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setServingStatus();
    }

    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = ExternalProfile::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = ExternalProfile::getPages();

        return $pages['view']->getPage();
    }

    /**
     * The index page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderIndexPage(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(ExternalProfileModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = ExternalProfileModel::factory()->count(10)->create();

        $this->get(ExternalProfile::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
    }

    /**
     * The create page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderCreatePage(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(ExternalProfileModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $this->get(ExternalProfile::getUrl('create'))
            ->assertSuccessful();
    }

    /**
     * The edit page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderEditPage(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(ExternalProfileModel::class),
            )
            ->createOne();

        $this->actingAs($user);

        $record = ExternalProfileModel::factory()->createOne();

        $this->get(ExternalProfile::getUrl('edit', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The view page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderViewPage(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(ExternalProfileModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = ExternalProfileModel::factory()->createOne();

        $this->get(ExternalProfile::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }
}
