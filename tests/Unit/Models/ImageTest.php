<?php

namespace Tests\Unit\Models;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The image shall be deleted from storage when the Image is deleted.
     *
     * @return void
     */
    public function testImageStorageDeletion()
    {
        $fs = Storage::fake('images');
        $file = File::fake()->image($this->faker->word());
        $path = $fs->put('', $file);

        $image = Image::factory()->create([
            'path' => $path,
        ]);

        $image->delete();

        $this->assertFalse($fs->exists($path));
    }
}
