<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Admin;

use App\Actions\Storage\Admin\Dump\DumpDocumentAction;
use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Constants\Config\DumpConstants;
use App\Constants\Config\FlagConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class LatestDocumentDumpTest.
 */
class LatestDocumentDumpTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * If dump downloading is disabled through the 'flags.allow_dump_downloading' property,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testDumpDownloadingNotAllowedForbidden(): void
    {
        Storage::fake('local');
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, false);

        $action = new DumpWikiAction();

        $action->handle();

        $response = $this->get(route('dump.latest.document.show'));

        $response->assertForbidden();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to download the latest document dump
     * even if the 'flags.allow_dump_downloading' property is disabled.
     *
     * @return void
     */
    public function testVideoStreamingPermittedForBypass(): void
    {
        Storage::fake('local');
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, $this->faker->boolean());

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpWikiAction();

            $action->handle();
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpDocumentAction();

            $action->handle();
        });

        $path = Str::of(DumpDocumentAction::FILENAME_PREFIX)
            ->append($this->faker->word())
            ->append('.sql')
            ->__toString();

        $file = File::fake()->create($path);
        $fsFile = $fs->putFileAs('', $file, $path);

        $dump = Dump::factory()->createOne([
            Dump::ATTRIBUTE_PATH => $fsFile,
        ]);

        $user = User::factory()->withPermission(SpecialPermission::BYPASS_FEATURE_FLAGS)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('dump.latest.document.show'));

        $response->assertDownload($dump->path);
    }

    /**
     * If no dumps exist, the user shall receive a not found error.
     *
     * @return void
     */
    public function testNotFoundIfNoDocumentDumps(): void
    {
        Storage::fake('local');
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

        $response = $this->get(route('dump.latest.document.show'));

        $response->assertNotFound();
    }

    /**
     * If no document dumps exist, the user shall receive a not found error.
     *
     * @return void
     */
    public function testNotFoundIfWikiDumpsExist(): void
    {
        Storage::fake('local');
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpWikiAction();

            $action->handle();
        });

        $response = $this->get(route('dump.latest.document.show'));

        $response->assertNotFound();
    }

    /**
     * If document dumps exist, the latest document dump is downloaded from storage through the response.
     *
     * @return void
     */
    public function testLatestDocumentDumpDownloaded(): void
    {
        Storage::fake('local');
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpWikiAction();

            $action->handle();
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpDocumentAction();

            $action->handle();
        });

        $path = Str::of(DumpDocumentAction::FILENAME_PREFIX)
            ->append($this->faker->word())
            ->append('.sql')
            ->__toString();

        $file = File::fake()->create($path);
        $fsFile = $fs->putFileAs('', $file, $path);

        $dump = Dump::factory()->createOne([
            Dump::ATTRIBUTE_PATH => $fsFile,
        ]);

        $response = $this->get(route('dump.latest.document.show'));

        $response->assertDownload($dump->path);
    }
}
