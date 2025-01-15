<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\List\ExternalProfile;
use App\Models\List\ExternalProfile as ExternalProfileModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListExternalProfiles.
 */
class ListExternalProfiles extends BaseListResources
{
    protected static string $resource = ExternalProfile::class;

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
            $search = preg_replace('/[^A-Za-z0-9 ]/', '', $search);
            $query->whereIn(ExternalProfileModel::ATTRIBUTE_ID, ExternalProfileModel::search($search)->take(25)->keys());
        }

        return $query;
    }
}
