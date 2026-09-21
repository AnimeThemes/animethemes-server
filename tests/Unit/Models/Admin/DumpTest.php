<?php

declare(strict_types=1);

use App\Models\Admin\Dump;

test('nameable', function (): void {
    $dump = Dump::factory()->createOne();

    expect($dump->getName())->toBeString();
});

test('has subtitle', function (): void {
    $dump = Dump::factory()->createOne();

    expect($dump->getSubtitle())->toBeString();
});
