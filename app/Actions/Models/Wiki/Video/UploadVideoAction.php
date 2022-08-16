<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Video;

use App\Actions\Models\ActionResult;
use App\Actions\Repositories\ReconcileResults;
use App\Actions\Repositories\Wiki\Video\ReconcileVideoRepositories;
use App\Contracts\Repositories\RepositoryInterface;
use App\Repositories\Eloquent\Wiki\VideoRepository as VideoDestinationRepository;
use App\Repositories\Storage\Wiki\VideoRepository as VideoSourceRepository;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction
{
    /**
     * Create a new action instance.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     */
    public function __construct(protected readonly UploadedFile $file, protected readonly string $path)
    {
    }

    /**
     * Handle action.
     *
     * @return ActionResult
     */
    public function handle(): ActionResult
    {
        $uploadResults = $this->upload();

        $uploadResults->toLog();

        $reconcileResults = $this->reconcileVideo();

        $reconcileResults->toLog();

        return $uploadResults->toActionResult();
    }

    /**
     * Upload the video to configured disks.
     *
     * @return UploadResults
     */
    protected function upload(): UploadResults
    {
        $results = [];

        $uploadDisks = Config::get('video.upload_disks');
        foreach ($uploadDisks as $uploadDisk) {
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk($uploadDisk);

            $result = $fs->putFileAs($this->path, $this->file, $this->file->getClientOriginalName());

            $results[$uploadDisk] = $result;
        }

        return new UploadResults($results);
    }

    /**
     * Reconcile video repository.
     *
     * @return ReconcileResults
     */
    protected function reconcileVideo(): ReconcileResults
    {
        $action = new ReconcileVideoRepositories();

        /** @var RepositoryInterface $sourceRepository */
        $sourceRepository = App::make(VideoSourceRepository::class);
        $sourceRepository->handleFilter('path', $this->path);

        /** @var RepositoryInterface $destinationRepository */
        $destinationRepository = App::make(VideoDestinationRepository::class);
        $destinationRepository->handleFilter('path', $this->path);

        return $action->reconcileRepositories($sourceRepository, $destinationRepository);
    }
}
