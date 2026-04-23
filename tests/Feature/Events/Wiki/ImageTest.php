<?php

declare(strict_types=1);

use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Models\Wiki\Image;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('image created event dispatched', function (): void {
    Image::factory()->createOne();

    Event::assertDispatched(ImageCreated::class);
});

test('image deleted event dispatched', function (): void {
    $image = Image::factory()->createOne();

    $image->delete();

    Event::assertDispatched(ImageDeleted::class);
});

test('image restored event dispatched', function (): void {
    $image = Image::factory()->createOne();

    $image->restore();

    Event::assertDispatched(ImageRestored::class);
});

test('image restores quietly', function (): void {
    $image = Image::factory()->createOne();

    $image->restore();

    Event::assertNotDispatched(ImageUpdated::class);
});

test('image updated event dispatched', function (): void {
    $image = Image::factory()->createOne();
    $changes = Image::factory()->makeOne();

    $image->fill($changes->getAttributes());
    $image->save();

    Event::assertDispatched(ImageUpdated::class);
});

test('image updated event embed fields', function (): void {
    $image = Image::factory()->createOne();
    $changes = Image::factory()->makeOne();

    $image->fill($changes->getAttributes());
    $image->save();

    Event::assertDispatched(ImageUpdated::class, function (ImageUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
