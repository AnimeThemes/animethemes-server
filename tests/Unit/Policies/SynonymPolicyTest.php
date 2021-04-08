<?php

namespace Tests\Unit\Policies;

use App\Models\Anime;
use App\Models\Synonym;
use App\Models\User;
use App\Policies\SynonymPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class SynonymPolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any synonym.
     *
     * @return void
     */
    public function testViewAny()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $policy = new SynonymPolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a synonym.
     *
     * @return void
     */
    public function testView()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertTrue($policy->view($viewer, $synonym));
        $this->assertTrue($policy->view($editor, $synonym));
        $this->assertTrue($policy->view($admin, $synonym));
    }

    /**
     * A contributor or admin may create a synonym.
     *
     * @return void
     */
    public function testCreate()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $policy = new SynonymPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a synonym.
     *
     * @return void
     */
    public function testUpdate()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->update($viewer, $synonym));
        $this->assertTrue($policy->update($editor, $synonym));
        $this->assertTrue($policy->update($admin, $synonym));
    }

    /**
     * A contributor or admin may delete a synonym.
     *
     * @return void
     */
    public function testDelete()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->delete($viewer, $synonym));
        $this->assertTrue($policy->delete($editor, $synonym));
        $this->assertTrue($policy->delete($admin, $synonym));
    }

    /**
     * A contributor or admin may restore a synonym.
     *
     * @return void
     */
    public function testRestore()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->restore($viewer, $synonym));
        $this->assertTrue($policy->restore($editor, $synonym));
        $this->assertTrue($policy->restore($admin, $synonym));
    }

    /**
     * A contributor or admin may force delete a synonym.
     *
     * @return void
     */
    public function testForceDelete()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $synonym));
        $this->assertFalse($policy->forceDelete($editor, $synonym));
        $this->assertTrue($policy->forceDelete($admin, $synonym));
    }
}
