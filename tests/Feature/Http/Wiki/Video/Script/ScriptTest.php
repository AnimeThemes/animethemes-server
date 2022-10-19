<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki\Video\Script;

use App\Constants\Config\FlagConstants;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

/**
 * Class ScriptTest.
 */
class ScriptTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * If script downloading is disabled through the 'flags.allow_script_downloading' property,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testScriptDownloadingNotAllowedForbidden(): void
    {
        Config::set(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG_QUALIFIED, false);

        $script = VideoScript::factory()->createOne();

        $response = $this->get(route('videoscript.show', ['videoscript' => $script]));

        $response->assertForbidden();
    }

    /**
     * If the script is soft-deleted, the user shall receive a not found exception.
     *
     * @return void
     */
    public function testCannotStreamSoftDeletedVideo(): void
    {
        Config::set(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG_QUALIFIED, true);

        $script = VideoScript::factory()->createOne();

        $script->delete();

        $response = $this->get(route('videoscript.show', ['videoscript' => $script]));

        $response->assertNotFound();
    }

    /**
     * If script downloading is enabled, the script is downloaded from storage through the response.
     *
     * @return void
     */
    public function testDownloadedThroughResponse(): void
    {
        Config::set(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG_QUALIFIED, true);

        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.txt');
        $fsFile = $fs->putFile('', $file);

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fsFile,
        ]);

        $response = $this->get(route('videoscript.show', ['videoscript' => $script]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
