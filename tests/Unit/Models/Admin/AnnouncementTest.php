<?php

declare(strict_types=1);

use App\Models\Admin\Announcement;

test('nameable', function () {
    $announcement = Announcement::factory()->createOne();

    static::assertIsString($announcement->getName());
});

test('has subtitle', function () {
    $announcement = Announcement::factory()->createOne();

    static::assertIsString($announcement->getSubtitle());
});
