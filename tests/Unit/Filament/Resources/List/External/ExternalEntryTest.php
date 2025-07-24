<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\List\External;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\List\External\ExternalEntry;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry as ExternalEntryModel;
use App\Models\List\ExternalProfile;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

class ExternalEntryTest extends BaseResourceTestCase
{
    /**
     * Initial setup for the tests.
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
        $pages = ExternalEntry::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = ExternalEntry::getPages();

        return $pages['view']->getPage();
    }

    /**
     * The index page of the resource shall be rendered.
     */
    public function testRenderIndexPage(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(ExternalEntryModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $profile = ExternalProfile::factory()->entries(3)->createOne();

        $records = $profile->externalentries;

        $this->get(ExternalEntry::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
    }

    /**
     * The view page of the resource shall be rendered.
     */
    public function testRenderViewPage(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(ExternalEntryModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $profile = ExternalProfile::factory()->entries(3)->createOne();

        $record = $profile->externalentries->first();

        $this->get(ExternalEntry::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The create action of the resource shall be mounted.
     */
    public function testMountEditAction(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(ExternalEntryModel::class),
            )
            ->createOne();

        $this->actingAs($user);

        $profile = ExternalProfile::factory()->entries(3)->createOne();

        $record = $profile->externalentries->first();

        Livewire::test(static::getIndexPage())
            ->mountAction(EditAction::class, ['record' => $record])
            ->assertActionMounted(EditAction::class);
    }
}
