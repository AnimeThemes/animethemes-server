<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\User;
use App\Models\Video;
use App\Policies\EntryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any entry.
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

        $policy = new EntryPolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an entry.
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertTrue($policy->view($read_only, $entry));
        $this->assertTrue($policy->view($contributor, $entry));
        $this->assertTrue($policy->view($admin, $entry));
    }

    /**
     * A contributor or admin may create an entry.
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

        $policy = new EntryPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an entry.
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->update($read_only, $entry));
        $this->assertTrue($policy->update($contributor, $entry));
        $this->assertTrue($policy->update($admin, $entry));
    }

    /**
     * A contributor or admin may delete an entry.
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->delete($read_only, $entry));
        $this->assertTrue($policy->delete($contributor, $entry));
        $this->assertTrue($policy->delete($admin, $entry));
    }

    /**
     * A contributor or admin may restore an entry.
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->restore($read_only, $entry));
        $this->assertTrue($policy->restore($contributor, $entry));
        $this->assertTrue($policy->restore($admin, $entry));
    }

    /**
     * A contributor or admin may force delete an entry.
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $entry));
        $this->assertTrue($policy->forceDelete($contributor, $entry));
        $this->assertTrue($policy->forceDelete($admin, $entry));
    }

    /**
     * A contributor or admin may attach any video to an entry.
     *
     * @return void
     */
    public function testAttachAnyVideo()
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->attachAnyVideo($read_only, $entry));
        $this->assertTrue($policy->attachAnyVideo($contributor, $entry));
        $this->assertTrue($policy->attachAnyVideo($admin, $entry));
    }

    /**
     * A contributor or admin may attach a video to an entry if not already attached.
     *
     * @return void
     */
    public function testAttachNewVideo()
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $video = Video::factory()->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->attachVideo($read_only, $entry, $video));
        $this->assertTrue($policy->attachVideo($contributor, $entry, $video));
        $this->assertTrue($policy->attachVideo($admin, $entry, $video));
    }

    /**
     * If a video is already attached to an entry, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingVideo()
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $video = Video::factory()->create();
        $entry->videos()->attach($video);
        $policy = new EntryPolicy();

        $this->assertFalse($policy->attachVideo($read_only, $entry, $video));
        $this->assertFalse($policy->attachVideo($contributor, $entry, $video));
        $this->assertFalse($policy->attachVideo($admin, $entry, $video));
    }

    /**
     * A contributor or admin may detach a video from an anime.
     *
     * @return void
     */
    public function testDetachVideo()
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $video = Video::factory()->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->detachVideo($read_only, $entry, $video));
        $this->assertTrue($policy->detachVideo($contributor, $entry, $video));
        $this->assertTrue($policy->detachVideo($admin, $entry, $video));
    }
}
