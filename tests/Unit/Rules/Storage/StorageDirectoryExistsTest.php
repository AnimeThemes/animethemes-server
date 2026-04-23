<?php

declare(strict_types=1);

use App\Rules\Storage\StorageDirectoryExistsRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

uses(WithFaker::class);

test('passes if directory exists', function (): void {
    $directory = fake()->word();

    $fs = Storage::fake(fake()->word());

    $fs->makeDirectory($directory);

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $directory],
        [$attribute => new StorageDirectoryExistsRule($fs)]
    );

    $this->assertTrue($validator->passes());
});

test('fails if directory does not exist', function (): void {
    $fs = Storage::fake(fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->word()],
        [$attribute => new StorageDirectoryExistsRule($fs)]
    );

    $this->assertFalse($validator->passes());
});
