<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document\Page\Pages;

use App\Filament\Resources\Document\Page;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditPage.
 */
class EditPage extends BaseEditResource
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
