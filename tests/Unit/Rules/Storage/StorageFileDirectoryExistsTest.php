<?php

declare(strict_types=1);

use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('passes if directory exists', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(fake()->word());

    $file = File::fake()->create(fake()->word());

    $path = $fs->putFile(fake()->word(), $file);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $path],
        [$attribute => new StorageFileDirectoryExistsRule($fs)]
    );

    $this->assertTrue($validator->passes());
});

test('fails if directory does not exist', function () {
    $fs = Storage::fake(fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->filePath()],
        [$attribute => new StorageFileDirectoryExistsRule($fs)]
    );

    $this->assertFalse($validator->passes());
});
