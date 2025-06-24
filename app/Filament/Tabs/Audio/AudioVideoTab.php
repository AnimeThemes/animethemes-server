<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Audio;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AudioVideoTab.
 */
class AudioVideoTab extends BaseTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'video-audio-tab';
    }

    /**
     * Get the displayable name of the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getLabel(): string
    {
        return __('filament.tabs.audio.video.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Audio::RELATION_VIDEOS);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Audio::query()->whereDoesntHave(Audio::RELATION_VIDEOS)->count();
    }
}
