<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Admin;

use App\Constants\Config\DumpConstants;
use App\Constants\Config\FlagConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class DumpTest.
 */
class DumpTest extends TestCase
{
    use WithFaker;

    /**
     * If dump downloading is disabled through the 'flags.allow_dump_downloading' property,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testDumpDownloadingNotAllowedForbidden(): void
    {
        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, false);

        $dump = Dump::factory()->createOne();

        $response = $this->get(route('dump.show', ['dump' => $dump]));

        $response->assertForbidden();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to download dumps
     * even if the 'flags.allow_dump_downloading' property is disabled.
     *
     * @return void
     */
    public function testVideoStreamingPermittedForBypass(): void
    {
        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, $this->faker->boolean());

        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.sql');
        $fsFile = $fs->putFile('', $file);

        $dump = Dump::factory()->createOne([
            Dump::ATTRIBUTE_PATH => $fsFile,
        ]);

        $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('dump.show', ['dump' => $dump]));

        $response->assertDownload($dump->path);
    }

    /**
     * If the dump is soft-deleted, the user shall receive a not found exception.
     *
     * @return void
     */
    public function testCannotDownloadSoftDeletedDump(): void
    {
        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

        $dump = Dump::factory()->createOne();

        $dump->delete();

        $response = $this->get(route('dump.show', ['dump' => $dump]));

        $response->assertNotFound();
    }

    /**
     * If dump downloading is enabled, the dump is downloaded from storage through the response.
     *
     * @return void
     */
    public function testDownloadedThroughResponse(): void
    {
        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));
        $file = File::fake()->create($this->faker->word().'.sql');
        $fsFile = $fs->putFile('', $file);

        $dump = Dump::factory()->createOne([
            Dump::ATTRIBUTE_PATH => $fsFile,
        ]);

        $response = $this->get(route('dump.show', ['dump' => $dump]));

        $response->assertDownload($dump->path);
    }
}
