<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Pivot\Morph\Imageable\ImageableCreated;
use App\Events\Pivot\Morph\Imageable\ImageableDeleted;
use App\Events\Pivot\Morph\Imageable\ImageableUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('imageable created sends discord notification', function () {
    $modelClass = Arr::random(Imageable::$imageables);

    $model = $modelClass::factory()->createOne();
    $image = Image::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ImageableCreated::class);

    $model->images()->attach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('imageable deleted sends discord notification', function () {
    $modelClass = Arr::random(Imageable::$imageables);

    $model = $modelClass::factory()->createOne();
    $image = Image::factory()->createOne();

    $model->images()->attach($image);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ImageableDeleted::class);

    $model->images()->detach($image);

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('imageable updated sends discord notification', function () {
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

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(ImageableUpdated::class);

    $imageable->fill($changes->getAttributes());
    $imageable->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
