<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Synonym\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Anime\Synonym;

/**
 * Class ViewSynonym.
 */
class ViewSynonym extends BaseViewResource
{
    protected static string $resource = Synonym::class;

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
