<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class DeleteScriptTest.
 */
class DeleteScriptTest extends TestCase
{
    use WithFaker;

    /**
     * The Delete Script Action shall fail if there are no deletions.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Config::set(VideoConstants::SCRIPT_DISK_QUALIFIED, []);
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $script = VideoScript::factory()->createOne();

        $action = new DeleteScriptAction($script);

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Delete Script Action shall pass if there are deletions.
     *
     * @return void
     */
    public function testPassed(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteScriptAction($script);

        $storageResults = $action->handle();

        $result = $storageResults->toActionResult();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
    }

    /**
     * The Delete Script Action shall delete the file from the configured disks.
     *
     * @return void
     */
    public function testDeletedFromDisk(): void
    {
        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteScriptAction($script);

        $action->handle();

        static::assertEmpty($fs->allFiles());
    }

    /**
     * The Delete Video Action shall delete the script.
     *
     * @return void
     */
    public function testVideoDeleted(): void
    {
        Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        $file = File::fake()->create($this->faker->word().'.txt', $this->faker->randomDigitNotNull());

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $file->path(),
        ]);

        $action = new DeleteScriptAction($script);

        $result = $action->handle();

        $action->then($result);

        static::assertSoftDeleted($script);
    }
}
