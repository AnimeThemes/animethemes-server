<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Video;

use App\Constants\Config\VideoConstants;
use App\Events\Wiki\Video\Script\VideoScriptForceDeleting;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScriptTest extends TestCase
{
    use WithFaker;

    /**
     * Scripts shall be nameable.
     */
    public function testNameable(): void
    {
        $script = VideoScript::factory()->createOne();

        static::assertIsString($script->getName());
    }

    /**
     * Scripts shall have subtitle.
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
     */
    public function testVideo(): void
    {
        $script = VideoScript::factory()
            ->for(Video::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $script->video());
        static::assertInstanceOf(Video::class, $script->video()->first());
    }

    /**
     * The script shall not be deleted from storage when the VideoScript is deleted.
     */
    public function testScriptStorageDeletion(): void
    {
        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());
        $fsFile = $fs->putFile('', $file);

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fsFile,
        ]);

        $script->delete();

        static::assertTrue($fs->exists($script->path));
    }

    /**
     * The script shall be deleted from storage when the VideoScript is force deleted.
     */
    public function testScriptStorageForceDeletion(): void
    {
        Event::fakeExcept(VideoScriptForceDeleting::class);

        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.ogg', $this->faker->randomDigitNotNull());
        $fsFile = $fs->putFile('', $file);

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fsFile,
        ]);

        $script->forceDelete();

        static::assertFalse($fs->exists($script->path));
    }
}
