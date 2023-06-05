<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Storage;

use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Class StorageFileDirectoryExistsTest.
 */
class StorageFileDirectoryExistsTest extends TestCase
{
    use WithFaker;

    /**
     * The Storage File Directory Exists Rule shall return true if the directory exists in the filesystem.
     *
     * @return void
     */
    public function testPassesIfDirectoryExists(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake($this->faker->word());

        $file = File::fake()->create($this->faker->word());

        $path = $fs->putFile($this->faker->word(), $file);

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $path],
            [$attribute => new StorageFileDirectoryExistsRule($fs)]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Storage File Directory Exists Rule shall return false if the directory does not exist in the filesystem.
     *
     * @return void
     */
    public function testFailsIfDirectoryDoesNotExist(): void
    {
        $fs = Storage::fake($this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->filePath()],
            [$attribute => new StorageFileDirectoryExistsRule($fs)]
        );

        static::assertFalse($validator->passes());
    }
}
