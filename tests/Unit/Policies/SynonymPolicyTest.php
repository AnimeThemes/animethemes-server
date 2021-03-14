<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Anime;
use App\Models\Synonym;
use App\Models\User;
use App\Policies\SynonymPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SynonymPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any synonym.
     *
     * @return void
     */
    public function testViewAny()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $policy = new SynonymPolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a synonym.
     *
     * @return void
     */
    public function testView()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertTrue($policy->view($read_only, $synonym));
        $this->assertTrue($policy->view($contributor, $synonym));
        $this->assertTrue($policy->view($admin, $synonym));
    }

    /**
     * A contributor or admin may create a synonym.
     *
     * @return void
     */
    public function testCreate()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $policy = new SynonymPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a synonym.
     *
     * @return void
     */
    public function testUpdate()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->update($read_only, $synonym));
        $this->assertTrue($policy->update($contributor, $synonym));
        $this->assertTrue($policy->update($admin, $synonym));
    }

    /**
     * A contributor or admin may delete a synonym.
     *
     * @return void
     */
    public function testDelete()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->delete($read_only, $synonym));
        $this->assertTrue($policy->delete($contributor, $synonym));
        $this->assertTrue($policy->delete($admin, $synonym));
    }

    /**
     * A contributor or admin may restore a synonym.
     *
     * @return void
     */
    public function testRestore()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->restore($read_only, $synonym));
        $this->assertTrue($policy->restore($contributor, $synonym));
        $this->assertTrue($policy->restore($admin, $synonym));
    }

    /**
     * A contributor or admin may force delete a synonym.
     *
     * @return void
     */
    public function testForceDelete()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new SynonymPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $synonym));
        $this->assertFalse($policy->forceDelete($contributor, $synonym));
        $this->assertTrue($policy->forceDelete($admin, $synonym));
    }
}
