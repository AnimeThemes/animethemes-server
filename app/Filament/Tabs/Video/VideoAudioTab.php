<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Builder;

class VideoAudioTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'video-audio-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.video.audio.name');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave(Video::RELATION_AUDIO)
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE->value, 'misc%');
    }

    public function getBadge(): int
    {
        return Video::query()
            ->whereDoesntHave(Video::RELATION_AUDIO)
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE->value, 'misc%')
            ->count();
    }
}
