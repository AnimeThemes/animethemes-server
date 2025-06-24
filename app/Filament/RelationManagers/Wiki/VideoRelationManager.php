<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Video as VideoResource;
use App\Models\Wiki\Video;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class VideoRelationManager.
 */
abstract class VideoRelationManager extends BaseRelationManager
{
    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Schema $schema): Schema
    {
        return VideoResource::form($schema);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->modifyQueryUsing(fn (Builder $query) => $query->with(VideoResource::getEloquentQuery()->getEagerLoads()))
                ->heading(VideoResource::getPluralLabel())
                ->modelLabel(VideoResource::getLabel())
                ->recordTitleAttribute(Video::ATTRIBUTE_FILENAME)
                ->columns(VideoResource::table($table)->getColumns())
                ->defaultSort(Video::TABLE . '.' . Video::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            ...VideoResource::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
            ...VideoResource::getRecordActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
            ...VideoResource::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            ...VideoResource::getTableActions(),
        ];
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    protected function canCreate(): bool
    {
        return false;
    }
}
