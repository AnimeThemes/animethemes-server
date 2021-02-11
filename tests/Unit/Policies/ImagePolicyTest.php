<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use App\Models\User;
use App\Policies\ImagePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ImagePolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any image.
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

        $policy = new ImagePolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertTrue($policy->view($read_only, $image));
        $this->assertTrue($policy->view($contributor, $image));
        $this->assertTrue($policy->view($admin, $image));
    }

    /**
     * A contributor or admin may create an image.
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

        $policy = new ImagePolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->update($read_only, $image));
        $this->assertTrue($policy->update($contributor, $image));
        $this->assertTrue($policy->update($admin, $image));
    }

    /**
     * A contributor or admin may delete an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->delete($read_only, $image));
        $this->assertTrue($policy->delete($contributor, $image));
        $this->assertTrue($policy->delete($admin, $image));
    }

    /**
     * A contributor or admin may restore an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->restore($read_only, $image));
        $this->assertTrue($policy->restore($contributor, $image));
        $this->assertTrue($policy->restore($admin, $image));
    }

    /**
     * A contributor or admin may force delete an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->forceDelete($read_only, $image));
        $this->assertTrue($policy->forceDelete($contributor, $image));
        $this->assertTrue($policy->forceDelete($admin, $image));
    }

    /**
     * A contributor or admin may attach any artist to an image.
     *
     * @return void
     */
    public function testAttachAnyArtist()
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnyArtist($read_only, $image));
        $this->assertTrue($policy->attachAnyArtist($contributor, $image));
        $this->assertTrue($policy->attachAnyArtist($admin, $image));
    }

    /**
     * A contributor or admin may attach an artist to an image if not already attached.
     *
     * @return void
     */
    public function testAttachNewArtist()
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

        $image = Image::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachArtist($read_only, $image, $artist));
        $this->assertTrue($policy->attachArtist($contributor, $image, $artist));
        $this->assertTrue($policy->attachArtist($admin, $image, $artist));
    }

    /**
     * If an artist is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingArtist()
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

        $image = Image::factory()->create();
        $artist = Artist::factory()->create();
        $image->artists()->attach($artist);
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachArtist($read_only, $image, $artist));
        $this->assertFalse($policy->attachArtist($contributor, $image, $artist));
        $this->assertFalse($policy->attachArtist($admin, $image, $artist));
    }

    /**
     * A contributor or admin may detach an artist from an image.
     *
     * @return void
     */
    public function testDetachArtist()
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

        $image = Image::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->detachArtist($read_only, $image, $artist));
        $this->assertTrue($policy->detachArtist($contributor, $image, $artist));
        $this->assertTrue($policy->detachArtist($admin, $image, $artist));
    }

    /**
     * A contributor or admin may attach any anime to an image.
     *
     * @return void
     */
    public function testAttachAnyAnime()
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnyAnime($read_only, $image));
        $this->assertTrue($policy->attachAnyAnime($contributor, $image));
        $this->assertTrue($policy->attachAnyAnime($admin, $image));
    }

    /**
     * A contributor or admin may attach an anime to an image if not already attached.
     *
     * @return void
     */
    public function testAttachNewAnime()
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

        $image = Image::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnime($read_only, $image, $anime));
        $this->assertTrue($policy->attachAnime($contributor, $image, $anime));
        $this->assertTrue($policy->attachAnime($admin, $image, $anime));
    }

    /**
     * If an anime is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingAnime()
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

        $image = Image::factory()->create();
        $anime = Anime::factory()->create();
        $image->anime()->attach($anime);
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnime($read_only, $image, $anime));
        $this->assertFalse($policy->attachAnime($contributor, $image, $anime));
        $this->assertFalse($policy->attachAnime($admin, $image, $anime));
    }

    /**
     * A contributor or admin may detach an anime from an image.
     *
     * @return void
     */
    public function testDetachAnime()
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

        $image = Image::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->detachAnime($read_only, $image, $anime));
        $this->assertTrue($policy->detachAnime($contributor, $image, $anime));
        $this->assertTrue($policy->detachAnime($admin, $image, $anime));
    }
}
