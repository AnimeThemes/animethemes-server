<?php

declare(strict_types=1);

use App\Models\Admin\Dump;

test('nameable', function () {
    $dump = Dump::factory()->createOne();

    $this->assertIsString($dump->getName());
});

test('has subtitle', function () {
    $dump = Dump::factory()->createOne();

    $this->assertIsString($dump->getSubtitle());
});
