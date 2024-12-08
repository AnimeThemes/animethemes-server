<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\HeaderActions\Base\EditHeaderAction;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;

/**
 * Class BaseViewResource.
 */
class BaseViewResource extends ViewRecord
{
    use HasRecentHistoryRecorder;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        $pages = static::$resource::getPages();

        if (Arr::has($pages, 'edit')) {
            $editPage = $pages['edit']->getPage();
            $action = (new $editPage)->getHeaderActions();
        } else {
            $action = [];
        }

        return array_merge(
            [
                EditHeaderAction::make()
                    ->visible(Arr::has($pages, 'edit')),
            ],
            $action,
        );
    }
}
