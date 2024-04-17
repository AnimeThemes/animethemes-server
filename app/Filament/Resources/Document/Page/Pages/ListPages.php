<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document\Page\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Document\Page;

/**
 * Class ListPages.
 */
class ListPages extends BaseListResources
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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
