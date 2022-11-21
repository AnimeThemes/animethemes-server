<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki\Video\Script;

use App\Actions\Http\Wiki\Video\Script\ScriptDownloadAction;
use App\Http\Controllers\Controller;
use App\Models\Wiki\Video\VideoScript;
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
        $action = new ScriptDownloadAction($script);

        return $action->download();
    }
}
