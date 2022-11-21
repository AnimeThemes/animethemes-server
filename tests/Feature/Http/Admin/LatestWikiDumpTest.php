<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Admin;

use App\Actions\Storage\Admin\Dump\DumpDocumentAction;
use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Constants\Config\DumpConstants;
use App\Constants\Config\FlagConstants;
use App\Models\Admin\Dump;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class LatestWikiDumpTest.
 */
class LatestWikiDumpTest extends TestCase
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

        $response = $this->get(route('dump.latest.wiki.show'));

        $response->assertForbidden();
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

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

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

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

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

        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

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
