<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Constants\Config\DumpConstants;
use App\Http\Controllers\Controller;
use App\Models\Admin\Dump;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
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
        /** @var FilesystemAdapter $fs */
        $fs = Storage::disk(Config::get(DumpConstants::DISK_QUALIFIED));

        return $fs->download($dump->path);
    }
}
