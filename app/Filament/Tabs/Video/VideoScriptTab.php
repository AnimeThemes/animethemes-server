<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Builder;

class VideoScriptTab extends BaseTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'video-script-tab';
    }

    /**
     * Get the displayable name of the tab.
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
}
