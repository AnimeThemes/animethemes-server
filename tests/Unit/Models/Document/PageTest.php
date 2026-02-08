<?php

declare(strict_types=1);

use App\Models\Document\Page;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('nameable', function () {
    $page = Page::factory()->createOne();

    $this->assertIsString($page->getName());
});

test('has subtitle', function () {
    $page = Page::factory()->createOne();

    $this->assertIsString($page->getSubtitle());
});

test('previous', function () {
    $page = Page::factory()
        ->for(Page::factory(), Page::RELATION_PREVIOUS)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $page->previous());
    $this->assertInstanceOf(Page::class, $page->previous()->first());
});

test('next', function () {
    $page = Page::factory()
        ->for(Page::factory(), Page::RELATION_NEXT)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $page->next());
    $this->assertInstanceOf(Page::class, $page->next()->first());
});
