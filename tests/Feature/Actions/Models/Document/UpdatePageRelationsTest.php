<?php

declare(strict_types=1);

use App\Actions\Models\Document\UpdatePageRelations;
use App\Models\Document\Page;

test('associates next of previous page', function (): void {
    $previous = Page::factory()->createOne();

    $page = Page::factory()
        ->for($previous, Page::RELATION_PREVIOUS)
        ->createOne();

    $action = new UpdatePageRelations();

    $action->handle($page);

    $previous->refresh();

    expect($previous->next()->is($page))->toBeTrue();
});

test('associates previous of next page', function (): void {
    $next = Page::factory()->createOne();

    $page = Page::factory()
        ->for($next, Page::RELATION_NEXT)
        ->createOne();

    $action = new UpdatePageRelations();

    $action->handle($page);

    $next->refresh();

    expect($next->previous()->is($page))->toBeTrue();
});
