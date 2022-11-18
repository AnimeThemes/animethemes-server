<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Http\Admin\Dump\DumpDownloadAction;
use App\Http\Controllers\Controller;
use App\Models\Admin\Dump;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class DumpController.
 */
class DumpController extends Controller
{
    /**
     * Download dump.
     *
     * @param  Dump  $dump
     * @return StreamedResponse
     */
    public function show(Dump $dump): StreamedResponse
    {
        $action = new DumpDownloadAction($dump);

        return $action->download();
    }
}
