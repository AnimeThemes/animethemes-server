<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Http\Admin\Dump\DumpDownloadAction;
use App\Http\Controllers\Controller;
use App\Models\Admin\Dump;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DumpController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Dump::class, 'dump');
    }

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
