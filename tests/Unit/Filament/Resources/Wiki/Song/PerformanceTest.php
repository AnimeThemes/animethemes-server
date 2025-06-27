<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Wiki\Song;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\HeaderActions\Base\ForceDeleteHeaderAction;
use App\Filament\HeaderActions\Base\RestoreHeaderAction;
use App\Filament\Resources\Wiki\Song\Performance;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance as PerformanceModel;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class PerformanceTest.
 */
class PerformanceTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Performance::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = Performance::getPages();

        return $pages['view']->getPage();
    }

    /**
     * The index page of the resource shall be rendered.
     *
     * @return void
     */
    public function test_render_index_page(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(PerformanceModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = PerformanceModel::factory()
            ->for(Song::factory())
            ->artist(Artist::factory()->createOne())
            ->create();

        $this->get(Performance::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords(collect([$records]));
    }

    /**
     * The view page of the resource shall be rendered.
     *
     * @return void
     */
    public function test_render_view_page(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(PerformanceModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = PerformanceModel::factory()
            ->for(Song::factory())
            ->artist(Artist::factory()->createOne())
            ->createOne();

        $this->get(Performance::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The create action of the resource shall be mounted.
     *
     * @return void
     */
    public function test_mount_create_action(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(PerformanceModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        Livewire::test(static::getIndexPage())
            ->mountAction('new performance')
            ->assertActionMounted('new performance');
    }

    /**
     * The create action of the resource shall be mounted.
     *
     * @return void
     */
    public function test_mount_edit_action(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(PerformanceModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = PerformanceModel::factory()
            ->for(Song::factory())
            ->artist(Artist::factory()->createOne())
            ->createOne();

        Livewire::test(static::getIndexPage())
            ->mountTableAction(EditAction::class, $record)
            ->assertTableActionMounted(EditAction::class);
    }

    /**
     * The user with no permissions cannot create a record.
     *
     * @return void
     */
    public function test_user_cannot_create_record(): void
    {
        Livewire::test(static::getIndexPage())
            ->assertActionHidden('new performance');
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function test_user_cannot_edit_record(): void
    {
        $record = PerformanceModel::factory()
            ->for(Song::factory())
            ->artist(Artist::factory()->createOne())
            ->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(EditAction::class, $record);
    }

    /**
     * The user with no permissions cannot delete a record.
     *
     * @return void
     */
    public function test_user_cannot_delete_record(): void
    {
        $record = PerformanceModel::factory()
            ->for(Song::factory())
            ->artist(Artist::factory()->createOne())
            ->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(DeleteHeaderAction::class);

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(DeleteAction::class, $record);
    }

    /**
     * The user with no permissions cannot restore a record.
     *
     * @return void
     */
    public function test_user_cannot_restore_record(): void
    {
        $record = PerformanceModel::factory()
            ->for(Song::factory())
            ->artist(Artist::factory()->createOne())
            ->createOne();

        $record->delete();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(RestoreHeaderAction::class);

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(RestoreAction::class, $record);
    }

    /**
     * The user with no permissions cannot force delete a record.
     *
     * @return void
     */
    public function test_user_cannot_force_delete_record(): void
    {
        $record = PerformanceModel::factory()
            ->for(Song::factory())
            ->artist(Artist::factory()->createOne())
            ->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(ForceDeleteHeaderAction::class);

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(ForceDeleteAction::class, $record);
    }
}
