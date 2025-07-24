<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Dump;
use Tests\TestCase;

class DumpTest extends TestCase
{
    /**
     * Dumps shall be nameable.
     */
    public function testNameable(): void
    {
        $dump = Dump::factory()->createOne();

        static::assertIsString($dump->getName());
    }

    /**
     * Dumps shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $dump = Dump::factory()->createOne();

        static::assertIsString($dump->getSubtitle());
    }
}
