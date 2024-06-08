<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Video;
use App\Filament\Tabs\Video\VideoAudioTab;
use App\Filament\Tabs\Video\VideoResolutionTab;
use App\Filament\Tabs\Video\VideoScriptTab;
use App\Filament\Tabs\Video\VideoSourceTab;
use App\Filament\Tabs\Video\VideoUnlinkedTab;
use App\Models\Wiki\Video as VideoModel;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListVideos.
 */
class ListVideos extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Video::class;

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
            [],
        );
    }

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $this->applyColumnSearchesToTableQuery($query);

        if (filled($search = $this->getTableSearch())) {
            $query->whereIn(VideoModel::ATTRIBUTE_ID, VideoModel::search($search)->take(25)->keys());
        }

        return $query;
    }

    /**
     * Get the tabs available.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            VideoAudioTab::class,
            VideoResolutionTab::class,
            VideoScriptTab::class,
            VideoSourceTab::class,
            VideoUnlinkedTab::class,
        ]);
    }
}
