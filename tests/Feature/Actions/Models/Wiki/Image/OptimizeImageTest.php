<?php

declare(strict_types=1);

use App\Actions\Models\Wiki\Image\OptimizeImageAction;
use App\Constants\Config\ImageConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('skipped', function (): void {
    $fs = Storage::fake(Config::get(ImageConstants::DISKS_QUALIFIED));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $action = new OptimizeImageAction($image);

    $result = $action->handle();

    expect($result->getStatus())->toBe(ActionStatus::SKIPPED);
    $this->assertDatabaseCount(Image::class, 1);
    expect($image->exists())->toBeTrue();
});

test('converts to avif', function (): void {
    $fs = Storage::fake(Config::get(ImageConstants::DISKS_QUALIFIED));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $action = new OptimizeImageAction($image, 'avif');

    $result = $action->handle();

    expect(Str::endsWith(($image->refresh()->path), '.avif'))->toBeTrue();
    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    $this->assertDatabaseCount(Image::class, 1);
    expect($image->exists())->toBeTrue();
});

test('downscale', function (): void {
    $fs = Storage::fake(Config::get(ImageConstants::DISKS_QUALIFIED));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $action = new OptimizeImageAction($image, null, fake()->randomDigitNotNull(), fake()->randomDigitNotNull());

    $result = $action->handle();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
    $this->assertDatabaseCount(Image::class, 1);
    expect($image->exists())->toBeTrue();
});
