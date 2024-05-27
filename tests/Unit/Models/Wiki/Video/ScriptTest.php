<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Video;

use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class ScriptTest.
 */
class ScriptTest extends TestCase
{
    /**
     * Scripts shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $script = VideoScript::factory()->createOne();

        static::assertIsString($script->getName());
    }

    /**
     * Scripts shall have subtitle.
     *
     * @return void
     */
    public function testHasSubtitle(): void
    {
        $script = VideoScript::factory()
            ->for(Video::factory())
            ->createOne();

        static::assertIsString($script->getSubtitle());
    }

    /**
     * Scripts shall belong to a Video.
     *
     * @return void
     */
    public function testVideo(): void
    {
        $script = VideoScript::factory()
            ->for(Video::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $script->video());
        static::assertInstanceOf(Video::class, $script->video()->first());
    }
}
