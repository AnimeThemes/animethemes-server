<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AudioTest.
 */
class AudioTest extends TestCase
{
    use WithFaker;

    /**
     * Audios shall be auditable.
     *
     * @return void
     */
    public function testAuditable(): void
    {
        Config::set('audit.console', true);

        $audio = Audio::factory()->createOne();

        static::assertEquals(1, $audio->audits()->count());
    }

    /**
     * Audios shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $audio = Audio::factory()->createOne();

        static::assertIsString($audio->getName());
    }
}
