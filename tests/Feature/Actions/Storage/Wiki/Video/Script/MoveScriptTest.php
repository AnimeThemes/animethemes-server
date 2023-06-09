<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class MoveScriptTest.
 */
class MoveScriptTest extends TestCase
{
    use WithFaker;

    /**
     * The Move Script Action shall fail if there are no moves.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Config::set(VideoConstants::SCRIPT_DISK_QUALIFIED, []);
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $script = VideoScript::factory()->createOne();

        $action = new MoveScriptAction($script, $this->faker->word());

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Move Script Action shall pass if there are moves.
     *
     * @return void
     */
    public function testPassed(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $directory = $this->faker->unique()->word();

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $action = new MoveScriptAction($script, Str::replace($directory, $this->faker->unique()->word(), $script->path));

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue(ActionStatus::PASSED === $result->getStatus());
    }

    /**
     * The Move Script Action shall move the file in the configured disks.
     *
     * @return void
     */
    public function testMovedInDisk(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $directory = $this->faker->unique()->word();

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $from = $script->path;
        $to = Str::replace($directory, $this->faker->unique()->word(), $script->path);

        $action = new MoveScriptAction($script, $to);

        $action->handle();

        $fs->assertMissing($from);
        $fs->assertExists($to);
    }

    /**
     * The Move Script Action shall move the script.
     *
     * @return void
     */
    public function testScriptUpdated(): void
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $directory = $this->faker->unique()->word();

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
        ]);

        $to = Str::replace($directory, $this->faker->unique()->word(), $script->path);

        $action = new MoveScriptAction($script, $to);

        $result = $action->handle();

        $action->then($result);

        static::assertDatabaseHas(VideoScript::class, [VideoScript::ATTRIBUTE_PATH => $to]);
    }
}
