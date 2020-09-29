<?php

namespace Tests\Feature;

use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoReconcileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * If no changes are needed, the Reconcile Video Command shall output 'No Videos created or deleted'
     *
     * @return void
     */
    public function testNoResultsForReconcileVideoCommand()
    {
        Storage::fake('spaces');

        $this->artisan('reconcile:video')->expectsOutput('No Videos created or deleted');
    }

    /**
     * The Reconcile Video Command shall filter objects for WebM metadata
     * Note: Here we are asserting that our file type filter is in place
     *
     * @return void
     */
    public function testDirectoryNoResultsForReconcileVideoCommand()
    {
        $fs = Storage::fake('spaces');

        $fs->makeDirectory($this->faker->word());

        $this->artisan('reconcile:video')->expectsOutput('No Videos created or deleted');
    }

    /**
     * The Reconcile Video Command shall filter objects for WebM metadata
     * Note: Here we are asserting that our file extension filter is in place
     *
     * @return void
     */
    public function testExtensionNoResultsForReconcileVideoCommand()
    {
        $fs = Storage::fake('spaces');

        $file = File::fake()->image($this->faker->word());
        $fs->put('', $file);

        $this->artisan('reconcile:video')->expectsOutput('No Videos created or deleted');
    }

    /**
     * If videos are created, the Reconcile Video Command shall output '{Created Count} Videos created, 0 Videos deleted'
     *
     * @return void
     */
    public function testCreatedForReconcileVideoCommand()
    {
        $fs = Storage::fake('spaces');

        $created_video_count = $this->faker->randomDigitNotNull;
        Collection::times($created_video_count)->each(function() use ($fs) {
            $file_name = $this->faker->unique()->word();
            $file = File::fake()->create($file_name . '.webm');
            $fs->put('', $file);
        });

        $this->artisan('reconcile:video')->expectsOutput("{$created_video_count} Videos created, 0 Videos deleted");
    }

    /**
     * If videos are created, the Reconcile Video Command shall output '0 Videos created, {Deleted Count} Videos deleted'
     *
     * @return void
     */
    public function testDeletedForReconcileVideoCommand()
    {
        $created_video_count = $this->faker->randomDigitNotNull;
        Video::factory()->count($created_video_count)->create();

        Storage::fake('spaces');

        $this->artisan('reconcile:video')->expectsOutput("0 Videos created, {$created_video_count} Videos deleted");
    }
}
