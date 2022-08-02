<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki;

use App\Rules\Wiki\StorageDirectoryExistsRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class StorageDirectoryExistsTest.
 */
class StorageDirectoryExistsTest extends TestCase
{
    use WithFaker;

    /**
     * The Storage Directory Exists Rule shall return true if the directory exists in the filesystem.
     *
     * @return void
     */
    public function testPassesIfDirectoryExists(): void
    {
        $directory = $this->faker->word();

        $fs = Storage::fake($this->faker->word());

        $fs->makeDirectory($directory);

        $rule = new StorageDirectoryExistsRule($fs);

        static::assertTrue($rule->passes($this->faker->word(), $directory));
    }

    /**
     * The Storage Directory Exists Rule shall return false if the directory does not exist in the filesystem.
     *
     * @return void
     */
    public function testFailsIfDirectoryDoesNotExist(): void
    {
        $fs = Storage::fake($this->faker->word());

        $rule = new StorageDirectoryExistsRule($fs);

        static::assertFalse($rule->passes($this->faker->word(), $this->faker->word()));
    }
}
