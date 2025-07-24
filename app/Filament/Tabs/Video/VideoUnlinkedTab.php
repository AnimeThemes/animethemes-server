<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Builder;

class VideoUnlinkedTab extends BaseTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'video-unlinked-tab';
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
        return __('filament.tabs.video.unlinked.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave(Video::RELATION_ANIMETHEMEENTRIES)
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE->value, 'misc%');
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Video::query()
            ->whereDoesntHave(Video::RELATION_ANIMETHEMEENTRIES)
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE->value, 'misc%')
            ->count();
    }
}
