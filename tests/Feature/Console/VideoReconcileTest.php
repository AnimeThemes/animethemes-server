<?php

namespace Tests\Feature\Console;

use App\Models\Video;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoReconcileTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * If no changes are needed, the Reconcile Video Command shall output 'No Videos created or deleted or updated'.
     *
     * @return void
     */
    public function testNoResultsForReconcileVideoCommand()
    {
        Storage::fake('videos');

        $this->artisan('reconcile:video')->expectsOutput('No Videos created or deleted or updated');
    }

    /**
     * The Reconcile Video Command shall filter objects for WebM metadata.
     * Note: Here we are asserting that our file type filter is in place.
     *
     * @return void
     */
    public function testDirectoryNoResultsForReconcileVideoCommand()
    {
        $fs = Storage::fake('videos');

        $fs->makeDirectory($this->faker->word());

        $this->artisan('reconcile:video')->expectsOutput('No Videos created or deleted or updated');
    }

    /**
     * The Reconcile Video Command shall filter objects for WebM metadata.
     * Note: Here we are asserting that our file extension filter is in place.
     *
     * @return void
     */
    public function testExtensionNoResultsForReconcileVideoCommand()
    {
        $fs = Storage::fake('videos');

        $file = File::fake()->image($this->faker->word());
        $fs->put('', $file);

        $this->artisan('reconcile:video')->expectsOutput('No Videos created or deleted or updated');
    }

    /**
     * If videos are created, the Reconcile Video Command shall output '{Created Count} Videos created, 0 Videos deleted, 0 Videos updated'.
     *
     * @return void
     */
    public function testCreatedForReconcileVideoCommand()
    {
        $fs = Storage::fake('videos');

        $created_video_count = $this->faker->randomDigitNotNull;
        Collection::times($created_video_count)->each(function () use ($fs) {
            $file_name = $this->faker->word();
            $file = File::fake()->create($file_name.'.webm');
            $fs->put('', $file);
        });

        $this->artisan('reconcile:video')->expectsOutput("{$created_video_count} Videos created, 0 Videos deleted, 0 Videos updated");
    }

    /**
     * If videos are deleted, the Reconcile Video Command shall output '0 Videos created, {Deleted Count} Videos deleted, 0 Videos updated'.
     *
     * @return void
     */
    public function testDeletedForReconcileVideoCommand()
    {
        $deleted_video_count = $this->faker->randomDigitNotNull;
        Video::factory()->count($deleted_video_count)->create();

        Storage::fake('videos');

        $this->artisan('reconcile:video')->expectsOutput("0 Videos created, {$deleted_video_count} Videos deleted, 0 Videos updated");
    }

    /**
     * If videos are updated, the Reconcile Video Command shall output '0 Videos created, 0 Videos deleted, {Updated Count} Videos updated'.
     *
     * @return void
     */
    public function testUpdatedForReconcileVideoCommand()
    {
        $fs = Storage::fake('videos');

        $updated_video_count = $this->faker->randomDigitNotNull;

        Collection::times($updated_video_count)->each(function () use ($fs) {
            $file_name = $this->faker->word();
            $file = File::fake()->create($file_name.'.webm');
            $fs_file = $fs->put('', $file);
            $fs_pathinfo = pathinfo(strval($fs_file));

            Video::create([
                'basename' => $fs_pathinfo['basename'],
                'filename' => $fs_pathinfo['filename'],
                'path' => $this->faker->word(),
                'size' => $this->faker->randomNumber(),
                'mimetype' => MimeType::fromFilename($fs_pathinfo['basename']),
            ]);
        });

        $this->artisan('reconcile:video')->expectsOutput("0 Videos created, 0 Videos deleted, {$updated_video_count} Videos updated");
    }
}
