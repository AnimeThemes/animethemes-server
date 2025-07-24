<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Storage;

use App\Rules\Storage\StorageDirectoryExistsRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StorageDirectoryExistsTest extends TestCase
{
    use WithFaker;

    /**
     * The Storage Directory Exists Rule shall return true if the directory exists in the filesystem.
     */
    public function testPassesIfDirectoryExists(): void
    {
        $directory = $this->faker->word();

        $fs = Storage::fake($this->faker->word());

        $fs->makeDirectory($directory);

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $directory],
            [$attribute => new StorageDirectoryExistsRule($fs)]
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Storage Directory Exists Rule shall return false if the directory does not exist in the filesystem.
     */
    public function testFailsIfDirectoryDoesNotExist(): void
    {
        $fs = Storage::fake($this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->word()],
            [$attribute => new StorageDirectoryExistsRule($fs)]
        );

        static::assertFalse($validator->passes());
    }
}
