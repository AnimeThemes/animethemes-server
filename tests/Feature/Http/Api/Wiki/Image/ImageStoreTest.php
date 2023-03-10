<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Image;

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ImageStoreTest.
 */
class ImageStoreTest extends TestCase
{
    use WithoutEvents;
    use WithFaker;

    /**
     * The Image Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $image = Image::factory()->makeOne();

        $response = $this->post(route('api.image.store', $image->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Image Store Endpoint shall forbid users without the create image permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $image = Image::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.image.store', $image->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Image Store Endpoint shall require the file field.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.image.store'));

        $response->assertJsonValidationErrors([
            ImageFileField::ATTRIBUTE_FILE,
        ]);
    }

    /**
     * The Image Store Endpoint shall create an image.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $fs = Storage::fake(Config::get('image.disk'));

        $parameters = [Image::ATTRIBUTE_FACET => ImageFacet::getRandomInstance()->description];

        $user = User::factory()->withPermission(CrudPermission::CREATE()->format(Image::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.image.store', $parameters), [
            ImageFileField::ATTRIBUTE_FILE => UploadedFile::fake()->image($this->faker->word().'.jpg'),
        ]);

        $response->assertCreated();
        static::assertCount(1, $fs->allFiles());
        static::assertDatabaseCount(Image::TABLE, 1);
    }
}
