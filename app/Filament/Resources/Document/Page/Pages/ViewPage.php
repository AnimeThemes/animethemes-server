<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document\Page\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Document\Page;

/**
 * Class ViewPage.
 */
class ViewPage extends BaseViewResource
{
    protected static string $resource = Page::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
