<?php

declare(strict_types=1);

use App\Events\Document\Page\PageCreated;
use App\Events\Document\Page\PageDeleted;
use App\Events\Document\Page\PageRestored;
use App\Events\Document\Page\PageUpdated;
use App\Models\Document\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('page created event dispatched', function (): void {
    Page::factory()->createOne();

    Event::assertDispatched(PageCreated::class);
});

test('page deleted event dispatched', function (): void {
    $page = Page::factory()->createOne();

    $page->delete();

    Event::assertDispatched(PageDeleted::class);
});

test('page restored event dispatched', function (): void {
    $page = Page::factory()->createOne();

    $page->restore();

    Event::assertDispatched(PageRestored::class);
});

test('page restores quietly', function (): void {
    $page = Page::factory()->createOne();

    $page->restore();

    Event::assertNotDispatched(PageUpdated::class);
});

test('page updated event dispatched', function (): void {
    $page = Page::factory()->createOne();
    $changes = Page::factory()->makeOne();

    $page->fill($changes->getAttributes());
    $page->save();

    Event::assertDispatched(PageUpdated::class);
});

test('page updated event embed fields', function (): void {
    $page = Page::factory()->createOne();
    $changes = Page::factory()->makeOne();

    $page->fill($changes->getAttributes());
    $page->save();

    Event::assertDispatched(PageUpdated::class, function (PageUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
