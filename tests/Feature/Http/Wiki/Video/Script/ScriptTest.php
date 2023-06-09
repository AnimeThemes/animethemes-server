<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki\Video\Script;

use App\Constants\Config\VideoConstants;
use App\Enums\Auth\SpecialPermission;
use App\Features\AllowScriptDownloading;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class ScriptTest.
 */
class ScriptTest extends TestCase
{
    use WithFaker;

    /**
     * If script downloading is disabled through the Allow Script Downloading feature,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testScriptDownloadingNotAllowedForbidden(): void
    {
        Feature::deactivate(AllowScriptDownloading::class);

        $script = VideoScript::factory()->createOne();

        $response = $this->get(route('videoscript.show', ['videoscript' => $script]));

        $response->assertForbidden();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to download scripts
     * even if the Allow Script Downloading feature is disabled.
     *
     * @return void
     */
    public function testVideoStreamingPermittedForBypass(): void
    {
        Feature::activate(AllowScriptDownloading::class, $this->faker->boolean());

        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.txt');
        $fsFile = $fs->putFile('', $file);

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fsFile,
        ]);

        $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS->value)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('videoscript.show', ['videoscript' => $script]));

        $response->assertDownload($script->path);
    }

    /**
     * If the script is soft-deleted, the user shall receive a not found exception.
     *
     * @return void
     */
    public function testCannotStreamSoftDeletedVideo(): void
    {
        Feature::activate(AllowScriptDownloading::class);

        $script = VideoScript::factory()->trashed()->createOne();

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
        Feature::activate(AllowScriptDownloading::class);

        $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.txt');
        $fsFile = $fs->putFile('', $file);

        $script = VideoScript::factory()->createOne([
            VideoScript::ATTRIBUTE_PATH => $fsFile,
        ]);

        $response = $this->get(route('videoscript.show', ['videoscript' => $script]));

        $response->assertDownload($script->path);
    }
}
