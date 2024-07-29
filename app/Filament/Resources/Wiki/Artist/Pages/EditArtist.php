<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\Pages;

use App\Filament\HeaderActions\Models\Wiki\Artist\AttachArtistResourceHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\AttachImageHeaderAction;
use App\Filament\Resources\Base\BaseEditResource;
use App\Filament\Resources\Wiki\Artist;
use Filament\Actions\ActionGroup;

/**
 * Class EditArtist.
 */
class EditArtist extends BaseEditResource
{
    protected static string $resource = Artist::class;

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
                    AttachImageHeaderAction::make('attach-artist-image'),

                    AttachArtistResourceHeaderAction::make('attach-artist-resource'),
                ])
            ],
        );
    }
}
