<?php

declare(strict_types=1);

use App\Actions\Models\Wiki\Image\OptimizeImageAction;
use App\Constants\Config\ImageConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Image;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('converts to avif', function () {
    $fs = Storage::fake(Config::get(ImageConstants::DISKS_QUALIFIED));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $action = new OptimizeImageAction($image, 'avif');

    $result = $action->handle();

    $this->assertTrue(Str::endsWith(($image->refresh()->path), '.avif'));
    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertDatabaseCount(Image::class, 1);
    $this->assertTrue($image->exists());
});

test('passes', function () {
    $fs = Storage::fake(Config::get(ImageConstants::DISKS_QUALIFIED));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $action = new OptimizeImageAction($image);

    $result = $action->handle();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
    $this->assertDatabaseCount(Image::class, 1);
    $this->assertTrue($image->exists());
})->only();
