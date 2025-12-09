<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Anime\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Submission\Resources\AnimeSubmissionResource;
use Filament\Actions\CreateAction;

class ListAnimeSubmissions extends BaseListResources
{
    /**
     * Get the header actions available.
     *
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected static string $resource = AnimeSubmissionResource::class;
}
