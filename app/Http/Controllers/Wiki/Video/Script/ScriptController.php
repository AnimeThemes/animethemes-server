<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki\Video\Script;

use App\Constants\Config\VideoConstants;
use App\Http\Controllers\Controller;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ScriptController.
 */
class ScriptController extends Controller
{
    /**
     * Download dump.
     *
     * @param  VideoScript  $script
     * @return StreamedResponse
     */
    public function show(VideoScript $script): StreamedResponse
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::disk(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        return $fs->download($script->path);
    }
}
