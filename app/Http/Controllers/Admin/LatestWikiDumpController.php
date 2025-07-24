<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Http\Admin\Dump\DumpDownloadAction;
use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Controllers\Controller;
use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LatestWikiDumpController extends Controller
{
    /**
     * Download dump.
     *
     * @throws ModelNotFoundException
     */
    public function show(): StreamedResponse
    {
        /** @var Dump $dump */
        $dump = Dump::query()
            ->where(Dump::ATTRIBUTE_PATH, ComparisonOperator::LIKE->value, DumpWikiAction::FILENAME_PREFIX.'%')
            ->latest()
            ->firstOrFail();

        $action = new DumpDownloadAction($dump);

        return $action->download();
    }
}
