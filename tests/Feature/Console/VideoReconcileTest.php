<?php

declare(strict_types=1);

namespace Console;

use App\Console\Commands\VideoReconcileCommand;
use App\Models\Video;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class VideoReconcileTest
 * @package Console
 */
class VideoReconcileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * If no changes are needed, the Reconcile Video Command shall output 'No Videos created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResults()
    {
        Storage::fake('videos');

        $this->artisan(VideoReconcileCommand::class)->expectsOutput('No Videos created or deleted or updated');
    }

    /**
     * The Reconcile Video Command shall filter objects for WebM metadata.
     * Note: Here we are asserting that our file type filter is in place.
     *
     * @return void
     */
    public function testDirectoryNoResults()
    {
        $fs = Storage::fake('videos');

        $fs->makeDirectory($this->faker->word());

        $this->artisan(VideoReconcileCommand::class)->expectsOutput('No Videos created or deleted or updated');
    }

    /**
     * The Reconcile Video Command shall filter objects for WebM metadata.
     * Note: Here we are asserting that our file extension filter is in place.
     *
     * @return void
     */
    public function testExtensionNoResults()
    {
        $fs = Storage::fake('videos');

        $file = File::fake()->image($this->faker->word());
        $fs->putFile('', $file);

        $this->artisan(VideoReconcileCommand::class)->expectsOutput('No Videos created or deleted or updated');
    }

    /**
     * If videos are created, the Reconcile Video Command shall output '{Created Count} Videos created, 0 Videos deleted, 0 Videos updated'.
     *
     * @return void
     */
    public function testCreated()
    {
        $fs = Storage::fake('videos');

        $createdVideoCount = $this->faker->randomDigitNotNull;
        Collection::times($createdVideoCount)->each(function () use ($fs) {
            $fileName = $this->faker->word();
            $file = File::fake()->create($fileName.'.webm');
            $fs->putFile('', $file);
        });

        $this->artisan(VideoReconcileCommand::class)->expectsOutput("{$createdVideoCount} Videos created, 0 Videos deleted, 0 Videos updated");
    }

    /**
     * If videos are deleted, the Reconcile Video Command shall output '0 Videos created, {Deleted Count} Videos deleted, 0 Videos updated'.
     *
     * @return void
     */
    public function testDeleted()
    {
        $deletedVideoCount = $this->faker->randomDigitNotNull;
        Video::factory()->count($deletedVideoCount)->create();

        Storage::fake('videos');

        $this->artisan(VideoReconcileCommand::class)->expectsOutput("0 Videos created, {$deletedVideoCount} Videos deleted, 0 Videos updated");
    }

    /**
     * If videos are updated, the Reconcile Video Command shall output '0 Videos created, 0 Videos deleted, {Updated Count} Videos updated'.
     *
     * @return void
     */
    public function testUpdated()
    {
        $fs = Storage::fake('videos');

        $updatedVideoCount = $this->faker->randomDigitNotNull;

        Collection::times($updatedVideoCount)->each(function () use ($fs) {
            $fileName = $this->faker->word();
            $file = File::fake()->create($fileName.'.webm');
            $fsFile = $fs->putFile('', $file);
            $fsPathinfo = pathinfo(strval($fsFile));

            Video::create([
                'basename' => $fsPathinfo['basename'],
                'filename' => $fsPathinfo['filename'],
                'path' => $this->faker->word(),
                'size' => $this->faker->randomNumber(),
                'mimetype' => MimeType::fromFilename($fsPathinfo['basename']),
            ]);
        });

        $this->artisan(VideoReconcileCommand::class)->expectsOutput("0 Videos created, 0 Videos deleted, {$updatedVideoCount} Videos updated");
    }
}
