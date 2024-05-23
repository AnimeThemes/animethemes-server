<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class VideoScriptTab.
 */
class VideoScriptTab extends BaseTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'video-script-tab';
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
        return __('filament.tabs.video.script.name');
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
            ->whereDoesntHave(Video::RELATION_SCRIPT)
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
            ->whereDoesntHave(Video::RELATION_SCRIPT)
            ->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE->value, 'misc%')
            ->count();
    }
}
