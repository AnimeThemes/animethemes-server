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
    public function test_nameable(): void
    {
        $dump = Dump::factory()->createOne();

        static::assertIsString($dump->getName());
    }

    /**
     * Dumps shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $dump = Dump::factory()->createOne();

        static::assertIsString($dump->getSubtitle());
    }
}
