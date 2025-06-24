<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\Pages;

use App\Filament\Actions\Models\Wiki\Artist\AttachArtistResourceAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Artist;
use Filament\Actions\ActionGroup;

/**
 * Class ViewArtist.
 */
class ViewArtist extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                AttachArtistResourceAction::make('attach-artist-resource'),
            ]),
        ];
    }
}
