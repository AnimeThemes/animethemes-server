<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Script\Pages;

use App\Filament\Resources\Wiki\Video\Script;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditScript.
 */
class EditScript extends BaseEditResource
{
    protected static string $resource = Script::class;

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
