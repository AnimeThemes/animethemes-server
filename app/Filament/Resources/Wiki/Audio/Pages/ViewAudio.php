<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Audio\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Audio;

/**
 * Class ViewAudio.
 */
class ViewAudio extends BaseViewResource
{
    protected static string $resource = Audio::class;

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
