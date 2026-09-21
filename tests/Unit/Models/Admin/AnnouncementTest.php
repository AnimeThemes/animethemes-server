<?php

declare(strict_types=1);

use App\Models\Admin\Announcement;

test('nameable', function (): void {
    $announcement = Announcement::factory()->createOne();

    expect($announcement->getName())->toBeString();
});

test('has subtitle', function (): void {
    $announcement = Announcement::factory()->createOne();

    expect($announcement->getSubtitle())->toBeString();
});
