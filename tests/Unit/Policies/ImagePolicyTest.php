<?php

namespace Tests\Unit\Policies;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Image;
use App\Models\User;
use App\Policies\ImagePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ImagePolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any image.
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

        $policy = new ImagePolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertTrue($policy->view($viewer, $image));
        $this->assertTrue($policy->view($editor, $image));
        $this->assertTrue($policy->view($admin, $image));
    }

    /**
     * A contributor or admin may create an image.
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

        $policy = new ImagePolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->update($viewer, $image));
        $this->assertTrue($policy->update($editor, $image));
        $this->assertTrue($policy->update($admin, $image));
    }

    /**
     * A contributor or admin may delete an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->delete($viewer, $image));
        $this->assertTrue($policy->delete($editor, $image));
        $this->assertTrue($policy->delete($admin, $image));
    }

    /**
     * A contributor or admin may restore an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->restore($viewer, $image));
        $this->assertTrue($policy->restore($editor, $image));
        $this->assertTrue($policy->restore($admin, $image));
    }

    /**
     * A contributor or admin may force delete an image.
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->forceDelete($viewer, $image));
        $this->assertFalse($policy->forceDelete($editor, $image));
        $this->assertTrue($policy->forceDelete($admin, $image));
    }

    /**
     * A contributor or admin may attach any artist to an image.
     *
     * @return void
     */
    public function testAttachAnyArtist()
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnyArtist($viewer, $image));
        $this->assertTrue($policy->attachAnyArtist($editor, $image));
        $this->assertTrue($policy->attachAnyArtist($admin, $image));
    }

    /**
     * A contributor or admin may attach an artist to an image if not already attached.
     *
     * @return void
     */
    public function testAttachNewArtist()
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

        $image = Image::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachArtist($viewer, $image, $artist));
        $this->assertTrue($policy->attachArtist($editor, $image, $artist));
        $this->assertTrue($policy->attachArtist($admin, $image, $artist));
    }

    /**
     * If an artist is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingArtist()
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

        $image = Image::factory()->create();
        $artist = Artist::factory()->create();
        $image->artists()->attach($artist);
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachArtist($viewer, $image, $artist));
        $this->assertFalse($policy->attachArtist($editor, $image, $artist));
        $this->assertFalse($policy->attachArtist($admin, $image, $artist));
    }

    /**
     * A contributor or admin may detach an artist from an image.
     *
     * @return void
     */
    public function testDetachArtist()
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

        $image = Image::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->detachArtist($viewer, $image, $artist));
        $this->assertTrue($policy->detachArtist($editor, $image, $artist));
        $this->assertTrue($policy->detachArtist($admin, $image, $artist));
    }

    /**
     * A contributor or admin may attach any anime to an image.
     *
     * @return void
     */
    public function testAttachAnyAnime()
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

        $image = Image::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnyAnime($viewer, $image));
        $this->assertTrue($policy->attachAnyAnime($editor, $image));
        $this->assertTrue($policy->attachAnyAnime($admin, $image));
    }

    /**
     * A contributor or admin may attach an anime to an image if not already attached.
     *
     * @return void
     */
    public function testAttachNewAnime()
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

        $image = Image::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnime($viewer, $image, $anime));
        $this->assertTrue($policy->attachAnime($editor, $image, $anime));
        $this->assertTrue($policy->attachAnime($admin, $image, $anime));
    }

    /**
     * If an anime is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingAnime()
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

        $image = Image::factory()->create();
        $anime = Anime::factory()->create();
        $image->anime()->attach($anime);
        $policy = new ImagePolicy();

        $this->assertFalse($policy->attachAnime($viewer, $image, $anime));
        $this->assertFalse($policy->attachAnime($editor, $image, $anime));
        $this->assertFalse($policy->attachAnime($admin, $image, $anime));
    }

    /**
     * A contributor or admin may detach an anime from an image.
     *
     * @return void
     */
    public function testDetachAnime()
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

        $image = Image::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ImagePolicy();

        $this->assertFalse($policy->detachAnime($viewer, $image, $anime));
        $this->assertTrue($policy->detachAnime($editor, $image, $anime));
        $this->assertTrue($policy->detachAnime($admin, $image, $anime));
    }
}
