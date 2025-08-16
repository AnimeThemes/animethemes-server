<?php

declare(strict_types=1);

use App\Events\Pivot\Morph\Imageable\ImageableCreated;
use App\Events\Pivot\Morph\Imageable\ImageableDeleted;
use App\Events\Pivot\Morph\Imageable\ImageableUpdated;
use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('imageable created event dispatched', function () {
    $modelClass = Arr::random(Imageable::$imageables);

    $model = $modelClass::factory()->createOne();
    $image = Image::factory()->createOne();

    $model->images()->attach($image);

    Event::assertDispatched(ImageableCreated::class);
});

test('imageable deleted event dispatched', function () {
    $modelClass = Arr::random(Imageable::$imageables);

    $model = $modelClass::factory()->createOne();
    $image = Image::factory()->createOne();

    $model->images()->attach($image);
    $model->images()->detach($image);

    Event::assertDispatched(ImageableDeleted::class);
});

test('imageable updated event dispatched', function () {
    $modelClass = Arr::random(Imageable::$imageables);

    $model = $modelClass::factory()->createOne();
    $image = Image::factory()->createOne();

    $imageable = Imageable::factory()
        ->for($model, Imageable::RELATION_IMAGEABLE)
        ->for($image, Imageable::RELATION_IMAGE)
        ->createOne();

    $changes = Imageable::factory()
        ->for($model, Imageable::RELATION_IMAGEABLE)
        ->for($image, Imageable::RELATION_IMAGE)
        ->makeOne();

    $imageable->fill($changes->getAttributes());
    $imageable->save();

    Event::assertDispatched(ImageableUpdated::class);
});

test('imageable updated event embed fields', function () {
    $modelClass = Arr::random(Imageable::$imageables);

    $model = $modelClass::factory()->createOne();
    $image = Image::factory()->createOne();

    $imageable = Imageable::factory()
        ->for($model, Imageable::RELATION_IMAGEABLE)
        ->for($image, Imageable::RELATION_IMAGE)
        ->createOne();

    $changes = Imageable::factory()
        ->for($model, Imageable::RELATION_IMAGEABLE)
        ->for($image, Imageable::RELATION_IMAGE)
        ->makeOne();

    $imageable->fill($changes->getAttributes());
    $imageable->save();

    Event::assertDispatched(ImageableUpdated::class, function (ImageableUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
