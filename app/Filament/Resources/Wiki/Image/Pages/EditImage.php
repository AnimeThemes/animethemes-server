<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Image\Pages;

use App\Filament\HeaderActions\Storage\Wiki\Image\MoveImageHeaderAction;
use App\Filament\Resources\Wiki\Image;
use App\Filament\Resources\Base\BaseEditResource;
use Filament\Actions\ActionGroup;

/**
 * Class EditImage.
 */
class EditImage extends BaseEditResource
{
    protected static string $resource = Image::class;

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
            [
                ActionGroup::make([
                    MoveImageHeaderAction::make('move-image'),
                ]),
            ],
        );
    }
}
