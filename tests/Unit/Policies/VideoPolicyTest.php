<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\User;
use App\Models\Video;
use App\Policies\VideoPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any video.
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

        $policy = new VideoPolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertTrue($policy->view($read_only, $video));
        $this->assertTrue($policy->view($contributor, $video));
        $this->assertTrue($policy->view($admin, $video));
    }

    /**
     * A contributor or admin may create a video.
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

        $policy = new VideoPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertFalse($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->update($read_only, $video));
        $this->assertTrue($policy->update($contributor, $video));
        $this->assertTrue($policy->update($admin, $video));
    }

    /**
     * A contributor or admin may delete a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->delete($read_only, $video));
        $this->assertFalse($policy->delete($contributor, $video));
        $this->assertTrue($policy->delete($admin, $video));
    }

    /**
     * A contributor or admin may restore a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->restore($read_only, $video));
        $this->assertFalse($policy->restore($contributor, $video));
        $this->assertTrue($policy->restore($admin, $video));
    }

    /**
     * A contributor or admin may force delete a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $video));
        $this->assertFalse($policy->forceDelete($contributor, $video));
        $this->assertTrue($policy->forceDelete($admin, $video));
    }

    /**
     * A contributor or admin may attach any entry to a video.
     *
     * @return void
     */
    public function testAttachAnyEntry()
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->attachAnyEntry($read_only, $video));
        $this->assertFalse($policy->attachAnyEntry($contributor, $video));
        $this->assertTrue($policy->attachAnyEntry($admin, $video));
    }

    /**
     * A contributor or admin may attach an entry to a video if not already attached.
     *
     * @return void
     */
    public function testAttachEntry()
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

        $video = Video::factory()->create();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->attachEntry($read_only, $video, $entry));
        $this->assertFalse($policy->attachEntry($contributor, $video, $entry));
        $this->assertTrue($policy->attachEntry($admin, $video, $entry));
    }

    /**
     * A contributor or admin may detach an entry from a video.
     *
     * @return void
     */
    public function testDetachEntry()
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

        $video = Video::factory()->create();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->detachEntry($read_only, $video, $entry));
        $this->assertFalse($policy->detachEntry($contributor, $video, $entry));
        $this->assertTrue($policy->detachEntry($admin, $video, $entry));
    }
}
