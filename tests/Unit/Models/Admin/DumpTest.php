<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Dump;
use Tests\TestCase;

/**
 * Class DumpTest.
 */
class DumpTest extends TestCase
{
    /**
     * Dumps shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $dump = Dump::factory()->createOne();

        static::assertIsString($dump->getName());
    }
}
