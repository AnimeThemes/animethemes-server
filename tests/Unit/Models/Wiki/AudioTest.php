<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Audio;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

    /**
     * Audios shall have a one-to-many polymorphic relationship to View.
     *
     * @return void
     */
    public function testViews(): void
    {
        $audio = Audio::factory()->createOne();

        views($audio)->record();

        static::assertInstanceOf(MorphMany::class, $audio->views());
        static::assertEquals(1, $audio->views()->count());
        static::assertInstanceOf(View::class, $audio->views()->first());
    }
}
