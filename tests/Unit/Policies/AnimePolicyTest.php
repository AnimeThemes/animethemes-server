<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Series;
use App\Models\User;
use App\Policies\AnimePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimePolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any anime.
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

        $policy = new AnimePolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an anime.
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertTrue($policy->view($read_only, $anime));
        $this->assertTrue($policy->view($contributor, $anime));
        $this->assertTrue($policy->view($admin, $anime));
    }

    /**
     * A contributor or admin may create an anime.
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

        $policy = new AnimePolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an anime.
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->update($read_only, $anime));
        $this->assertTrue($policy->update($contributor, $anime));
        $this->assertTrue($policy->update($admin, $anime));
    }

    /**
     * A contributor or admin may delete an anime.
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->delete($read_only, $anime));
        $this->assertTrue($policy->delete($contributor, $anime));
        $this->assertTrue($policy->delete($admin, $anime));
    }

    /**
     * A contributor or admin may restore an anime.
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->restore($read_only, $anime));
        $this->assertTrue($policy->restore($contributor, $anime));
        $this->assertTrue($policy->restore($admin, $anime));
    }

    /**
     * A contributor or admin may force delete an anime.
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->forceDelete($read_only, $anime));
        $this->assertFalse($policy->forceDelete($contributor, $anime));
        $this->assertTrue($policy->forceDelete($admin, $anime));
    }

    /**
     * A contributor or admin may attach any series to an anime.
     *
     * @return void
     */
    public function testAttachAnySeries()
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachAnySeries($read_only, $anime));
        $this->assertTrue($policy->attachAnySeries($contributor, $anime));
        $this->assertTrue($policy->attachAnySeries($admin, $anime));
    }

    /**
     * A contributor or admin may attach a series to an anime if not already attached.
     *
     * @return void
     */
    public function testAttachNewSeries()
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

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachSeries($read_only, $anime, $series));
        $this->assertTrue($policy->attachSeries($contributor, $anime, $series));
        $this->assertTrue($policy->attachSeries($admin, $anime, $series));
    }

    /**
     * If a series is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingSeries()
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

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();
        $anime->series()->attach($series);
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachSeries($read_only, $anime, $series));
        $this->assertFalse($policy->attachSeries($contributor, $anime, $series));
        $this->assertFalse($policy->attachSeries($admin, $anime, $series));
    }

    /**
     * A contributor or admin may detach a series from an anime.
     *
     * @return void
     */
    public function testDetachSeries()
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

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->detachSeries($read_only, $anime, $series));
        $this->assertTrue($policy->detachSeries($contributor, $anime, $series));
        $this->assertTrue($policy->detachSeries($admin, $anime, $series));
    }

    /**
     * A contributor or admin may attach any resource to an anime.
     *
     * @return void
     */
    public function testAttachAnyResource()
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachAnyExternalResource($read_only, $anime));
        $this->assertTrue($policy->attachAnyExternalResource($contributor, $anime));
        $this->assertTrue($policy->attachAnyExternalResource($admin, $anime));
    }

    /**
     * A contributor or admin may attach a resource to an anime.
     *
     * @return void
     */
    public function testAttachResource()
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

        $anime = Anime::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachExternalResource($read_only, $anime, $resource));
        $this->assertTrue($policy->attachExternalResource($contributor, $anime, $resource));
        $this->assertTrue($policy->attachExternalResource($admin, $anime, $resource));
    }

    /**
     * A contributor or admin may detach a resource from an anime.
     *
     * @return void
     */
    public function testDetachResource()
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

        $anime = Anime::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->detachExternalResource($read_only, $anime, $resource));
        $this->assertTrue($policy->detachExternalResource($contributor, $anime, $resource));
        $this->assertTrue($policy->detachExternalResource($admin, $anime, $resource));
    }

    /**
     * A contributor or admin may attach any image to an anime.
     *
     * @return void
     */
    public function testAttachAnyImage()
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

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachAnyImage($read_only, $anime));
        $this->assertTrue($policy->attachAnyImage($contributor, $anime));
        $this->assertTrue($policy->attachAnyImage($admin, $anime));
    }

    /**
     * A contributor or admin may attach an image to an anime if not already attached.
     *
     * @return void
     */
    public function testAttachNewImage()
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

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachImage($read_only, $anime, $image));
        $this->assertTrue($policy->attachImage($contributor, $anime, $image));
        $this->assertTrue($policy->attachImage($admin, $anime, $image));
    }

    /**
     * If an image is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingImage()
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

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();
        $anime->images()->attach($image);
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachImage($read_only, $anime, $image));
        $this->assertFalse($policy->attachImage($contributor, $anime, $image));
        $this->assertFalse($policy->attachImage($admin, $anime, $image));
    }

    /**
     * A contributor or admin may detach an image from an anime.
     *
     * @return void
     */
    public function testDetachImage()
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

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->detachImage($read_only, $anime, $image));
        $this->assertTrue($policy->detachImage($contributor, $anime, $image));
        $this->assertTrue($policy->detachImage($admin, $anime, $image));
    }
}
