<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Audio;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Builder;

class AudioVideoTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'video-audio-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.audio.video.name');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Audio::RELATION_VIDEOS);
    }

    public function getBadge(): int
    {
        return Audio::query()->whereDoesntHave(Audio::RELATION_VIDEOS)->count();
    }
}
