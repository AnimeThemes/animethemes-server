<?php

declare(strict_types=1);

use App\Models\Document\Page;

test('nameable', function () {
    $page = Page::factory()->createOne();

    $this->assertIsString($page->getName());
});

test('has subtitle', function () {
    $page = Page::factory()->createOne();

    $this->assertIsString($page->getSubtitle());
});
