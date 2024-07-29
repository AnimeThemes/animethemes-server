<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\HeaderActions\Discord\DiscordThreadHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Anime\AttachAnimeResourceHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\Anime\BackfillAnimeHeaderAction;
use App\Filament\HeaderActions\Models\Wiki\AttachImageHeaderAction;
use App\Filament\Resources\Wiki\Anime;
use App\Filament\Resources\Base\BaseEditResource;
use Filament\Actions\ActionGroup;

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
        $streamingResourceSites = [
            ResourceSite::CRUNCHYROLL,
            ResourceSite::HIDIVE,
            ResourceSite::NETFLIX,
            ResourceSite::DISNEY_PLUS,
            ResourceSite::HULU,
            ResourceSite::AMAZON_PRIME_VIDEO,
        ];

        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    DiscordThreadHeaderAction::make('discord-thread-header'),

                    BackfillAnimeHeaderAction::make('backfill-anime'),

                    AttachImageHeaderAction::make('attach-anime-image'),

                    AttachAnimeResourceHeaderAction::make('attach-anime-resource'),

                    AttachAnimeResourceHeaderAction::make('attach-anime-streaming-resource')
                        ->label(__('filament.actions.models.wiki.attach_streaming_resource.name'))
                        ->icon('heroicon-o-tv')
                        ->sites($streamingResourceSites),
                ]),
            ],
        );
    }
}
