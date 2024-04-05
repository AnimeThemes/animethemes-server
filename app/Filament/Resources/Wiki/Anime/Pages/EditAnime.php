<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Filament\Resources\Wiki\Anime;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditAnime.
 */
class EditAnime extends BaseEditResource
{
    protected static string $resource = Anime::class;

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
