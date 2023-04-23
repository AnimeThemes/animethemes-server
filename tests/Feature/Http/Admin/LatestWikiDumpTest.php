<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Admin;

use App\Actions\Storage\Admin\Dump\DumpDocumentAction;
use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Auth\SpecialPermission;
use App\Features\AllowDumpDownloading;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class LatestWikiDumpTest.
 */
class LatestWikiDumpTest extends TestCase
{
    use WithFaker;

    /**
     * If dump downloading is disabled through the Allow Dump Downloading feature,
     * the user shall receive a forbidden exception.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testDumpDownloadingNotAllowedForbidden(): void
    {
        Storage::fake('local');
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Feature::deactivate(AllowDumpDownloading::class);

        $action = new DumpWikiAction();

        $action->handle();

        $response = $this->get(route('dump.latest.wiki.show'));

        $response->assertForbidden();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to download the latest wiki dump
     * even if the Allow Dump Downloading feature is disabled.
     *
     * @return void
     */
    public function testVideoStreamingPermittedForBypass(): void
    {
        Storage::fake('local');
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Feature::activate(AllowDumpDownloading::class, $this->faker->boolean());

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpDocumentAction();

            $action->handle();
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpWikiAction();

            $action->handle();
        });

        $path = Str::of(DumpWikiAction::FILENAME_PREFIX)
            ->append($this->faker->word())
            ->append('.sql')
            ->__toString();

        $file = File::fake()->create($path);
        $fsFile = $fs->putFileAs('', $file, $path);

        $dump = Dump::factory()->createOne([
            Dump::ATTRIBUTE_PATH => $fsFile,
        ]);

        $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('dump.latest.wiki.show'));

        $response->assertDownload($dump->path);
    }

    /**
     * If no dumps exist, the user shall receive a not found error.
     *
     * @return void
     */
    public function testNotFoundIfNoWikiDumps(): void
    {
        Storage::fake('local');
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Feature::activate(AllowDumpDownloading::class);

        $response = $this->get(route('dump.latest.wiki.show'));

        $response->assertNotFound();
    }

    /**
     * If no wiki dumps exist, the user shall receive a not found error.
     *
     * @return void
     */
    public function testNotFoundIfDocumentDumpsExist(): void
    {
        Storage::fake('local');
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Feature::activate(AllowDumpDownloading::class);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpDocumentAction();

            $action->handle();
        });

        $response = $this->get(route('dump.latest.wiki.show'));

        $response->assertNotFound();
    }

    /**
     * If wiki dumps exist, the latest wiki dump is downloaded from storage through the response.
     *
     * @return void
     */
    public function testLatestWikiDumpDownloaded(): void
    {
        Storage::fake('local');
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Feature::activate(AllowDumpDownloading::class);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpDocumentAction();

            $action->handle();
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            $action = new DumpWikiAction();

            $action->handle();
        });

        $path = Str::of(DumpWikiAction::FILENAME_PREFIX)
            ->append($this->faker->word())
            ->append('.sql')
            ->__toString();

        $file = File::fake()->create($path);
        $fsFile = $fs->putFileAs('', $file, $path);

        $dump = Dump::factory()->createOne([
            Dump::ATTRIBUTE_PATH => $fsFile,
        ]);

        $response = $this->get(route('dump.latest.wiki.show'));

        $response->assertDownload($dump->path);
    }
}
