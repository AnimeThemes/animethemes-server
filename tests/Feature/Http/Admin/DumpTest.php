<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Admin;

use App\Constants\Config\DumpConstants;
use App\Constants\Config\FlagConstants;
use App\Models\Admin\Dump;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

/**
 * Class DumpTest.
 */
class DumpTest extends TestCase
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
        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, false);

        $dump = Dump::factory()->createOne();

        $response = $this->get(route('dump.show', ['dump' => $dump]));

        $response->assertForbidden();
    }

    /**
     * If the dump is soft-deleted, the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testSoftDeleteDumpDownloadingForbidden(): void
    {
        Config::set(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED, true);

        $dump = Dump::factory()->createOne();

        $dump->delete();

        $response = $this->get(route('dump.show', ['dump' => $dump]));

        $response->assertForbidden();
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

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
