<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Discord;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\Resources\Discord\DiscordThread;
use App\Models\Auth\User;
use App\Models\Discord\DiscordThread as DiscordThreadModel;
use App\Models\Wiki\Anime;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class DiscordThreadTest.
 */
class DiscordThreadTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = DiscordThread::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = DiscordThread::getPages();

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
     * The create page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderCreatePage(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(DiscordThreadModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $this->get(DiscordThread::getUrl('create'))
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
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(DiscordThreadModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = DiscordThreadModel::factory()
            ->for(Anime::factory())
            ->createOne();

        $this->get(DiscordThread::getUrl('edit', ['record' => $record]))
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
     * The user with no permissions cannot create a record.
     *
     * @return void
     */
    public function testUserCannotCreateRecord(): void
    {
        $this->get(DiscordThread::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function testUserCannotEditRecord(): void
    {
        $record = DiscordThreadModel::factory()
            ->for(Anime::factory())
            ->createOne();

        $this->get(DiscordThread::getUrl('edit', ['record' => $record]))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot delete a record.
     *
     * @return void
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = DiscordThreadModel::factory()
            ->for(Anime::factory())
            ->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(DeleteHeaderAction::class);
    }
}
