<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki\Video\Script;

use App\Actions\Http\Wiki\Video\Script\ScriptDownloadAction;
use App\Http\Controllers\Controller;
use App\Models\Wiki\Video\VideoScript;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScriptController extends Controller
{
    /**
     * Download dump.
     */
    public function show(VideoScript $videoscript): StreamedResponse
    {
        $action = new ScriptDownloadAction($videoscript);

        return $action->download();
    }
}
