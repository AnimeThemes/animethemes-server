<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Repositories\ReconcileResults;
use App\Actions\Storage\Base\UploadAction;
use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends UploadAction
{
    use ReconcilesVideoRepositories;

    /**
     * Create a new action instance.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  AnimeThemeEntry|null  $entry
     */
    public function __construct(UploadedFile $file, string $path, protected ?AnimeThemeEntry $entry = null)
    {
        parent::__construct($file, $path);
    }

    /**
     * Processes to be completed after handling action.
     *
     * @param  StorageResults  $storageResults
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function then(StorageResults $storageResults): void
    {
        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();

        if ($reconcileResults instanceof ReconcileResults && $this->entry !== null) {
            $video = $reconcileResults->getCreated()->firstWhere(Video::ATTRIBUTE_BASENAME, $this->file->getClientOriginalName());
            if ($video instanceof Video) {
                $video->animethemeentries()->attach($this->entry);
            }
        }
    }

    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Config::get(VideoConstants::DISKS_QUALIFIED);
    }
}
