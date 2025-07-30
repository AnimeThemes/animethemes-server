<?php

declare(strict_types=1);

use App\Rules\Storage\StorageDirectoryExistsRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('passes if directory exists', function () {
    $directory = fake()->word();

    $fs = Storage::fake(fake()->word());

    $fs->makeDirectory($directory);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $directory],
        [$attribute => new StorageDirectoryExistsRule($fs)]
    );

    static::assertTrue($validator->passes());
});

test('fails if directory does not exist', function () {
    $fs = Storage::fake(fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->word()],
        [$attribute => new StorageDirectoryExistsRule($fs)]
    );

    static::assertFalse($validator->passes());
});
