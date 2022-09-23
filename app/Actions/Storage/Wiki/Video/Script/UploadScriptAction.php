<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video\Script;

use App\Actions\Repositories\ReconcileResults;
use App\Actions\Storage\Base\UploadAction;
use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Constants\Config\VideoConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Class UploadScriptAction.
 */
class UploadScriptAction extends UploadAction
{
    use ReconcilesScriptRepositories;

    /**
     * Create a new action instance.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  Video|null  $video
     */
    public function __construct(UploadedFile $file, string $path, protected ?Video $video = null)
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

        // The script was successfully uploaded and reconciled into the database, so we can attempt further actions
        if ($reconcileResults instanceof ReconcileResults) {
            $this->attachVideo($reconcileResults);
        }
    }

    /**
     * Attach video if uploaded from Upload Video Action.
     *
     * @param  ReconcileResults  $reconcileResults
     * @return void
     */
    protected function attachVideo(ReconcileResults $reconcileResults): void
    {
        $path = Str::of($this->path)
            ->finish('/')
            ->append($this->file->getClientOriginalName())
            ->__toString();

        $script = $reconcileResults->getCreated()->firstWhere(VideoScript::ATTRIBUTE_PATH, $path);

        if ($script instanceof VideoScript && $this->video !== null) {
            $script->video()->associate($this->video)->save();
        }
    }

    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Arr::wrap(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    }
}
