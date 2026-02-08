<?php

declare(strict_types=1);

use App\Actions\Models\Document\UpdatePageRelations;
use App\Models\Document\Page;

test('associates next of previous page', function () {
    $previous = Page::factory()->createOne();

    $page = Page::factory()
        ->for($previous, Page::RELATION_PREVIOUS)
        ->createOne();

    $action = new UpdatePageRelations();

    $action->handle($page);

    $previous->refresh();

    $this->assertTrue($previous->next()->is($page));
});

test('associates previous of next page', function () {
    $next = Page::factory()->createOne();

    $page = Page::factory()
        ->for($next, Page::RELATION_NEXT)
        ->createOne();

    $action = new UpdatePageRelations();

    $action->handle($page);

    $next->refresh();

    $this->assertTrue($next->previous()->is($page));
});
