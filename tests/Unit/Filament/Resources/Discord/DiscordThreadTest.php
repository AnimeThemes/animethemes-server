<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Discord;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\Discord\DiscordThread;
use App\Models\Auth\User;
use App\Models\Discord\DiscordThread as DiscordThreadModel;
use App\Models\Wiki\Anime;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

class DiscordThreadTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     */
    protected static function getIndexPage(): string
    {
        $pages = DiscordThread::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     */
    protected static function getViewPage(): string
    {
        $pages = DiscordThread::getPages();

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
                CrudPermission::VIEW->format(DiscordThreadModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = DiscordThreadModel::factory()
            ->for(Anime::factory())
            ->count(10)
            ->create();

        $this->get(DiscordThread::getUrl('index'))
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
                CrudPermission::VIEW->format(DiscordThreadModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = DiscordThreadModel::factory()
            ->for(Anime::factory())
            ->createOne();

        $this->get(DiscordThread::getUrl('view', ['record' => $record]))
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
                CrudPermission::CREATE->format(DiscordThreadModel::class)
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
                CrudPermission::UPDATE->format(DiscordThreadModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = DiscordThreadModel::factory()
            ->for(Anime::factory())
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
        $record = DiscordThreadModel::factory()
            ->for(Anime::factory())
            ->createOne();

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
    }

    /**
     * The user with no permissions cannot delete a record.
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = DiscordThreadModel::factory()
            ->for(Anime::factory())
            ->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(DeleteAction::class);

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
    }
}
