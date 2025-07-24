<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Wiki\Song;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Song\Membership;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership as MembershipModel;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

class MembershipTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Membership::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = Membership::getPages();

        return $pages['view']->getPage();
    }

    /**
     * The index page of the resource shall be rendered.
     */
    public function testRenderIndexPage(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(MembershipModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = MembershipModel::factory()
            ->count(10)
            ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
            ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
            ->create();

        $this->get(Membership::getUrl('index'))
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
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(MembershipModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = MembershipModel::factory()
            ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
            ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
            ->createOne();

        $this->get(Membership::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The create action of the resource shall be mounted.
     */
    public function testMountCreateAction(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(MembershipModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        Livewire::test(static::getIndexPage())
            ->mountAction(CreateAction::class)
            ->assertActionMounted(CreateAction::class);
    }

    /**
     * The create action of the resource shall be mounted.
     */
    public function testMountEditAction(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(MembershipModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = MembershipModel::factory()
            ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
            ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
            ->createOne();

        Livewire::test(static::getIndexPage())
            ->mountAction(EditAction::class, ['record' => $record])
            ->assertActionMounted(EditAction::class);
    }

    /**
     * The user with no permissions cannot create a record.
     */
    public function testUserCannotCreateRecord(): void
    {
        Livewire::test(static::getIndexPage())
            ->assertActionHidden(CreateAction::class);
    }

    /**
     * The user with no permissions cannot edit a record.
     */
    public function testUserCannotEditRecord(): void
    {
        $record = MembershipModel::factory()
            ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
            ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
            ->createOne();

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
    }

    /**
     * The user with no permissions cannot delete a record.
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = MembershipModel::factory()
            ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
            ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
            ->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(DeleteAction::class);

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
    }

    /**
     * The user with no permissions cannot restore a record.
     */
    public function testUserCannotRestoreRecord(): void
    {
        $record = MembershipModel::factory()
            ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
            ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
            ->createOne();

        $record->delete();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(RestoreAction::class);

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
    }

    /**
     * The user with no permissions cannot force delete a record.
     */
    public function testUserCannotForceDeleteRecord(): void
    {
        $record = MembershipModel::factory()
            ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
            ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
            ->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(ForceDeleteAction::class);

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
    }
}
