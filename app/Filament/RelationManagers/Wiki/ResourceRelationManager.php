<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\ExternalResource as ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ResourceRelationManager.
 */
abstract class ResourceRelationManager extends BaseRelationManager
{
    /**
     * Get the pivot fields of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotFields(): array
    {
        return [
            TextInput::make(AnimeResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.anime.resources.as.name'))
                ->helperText(__('filament.fields.anime.resources.as.help')),
        ];
    }

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Form $form): Form
    {
        return ExternalResourceResource::form($form);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ExternalResourceResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ExternalResourceResource::getPluralLabel())
                ->modelLabel(ExternalResourceResource::getLabel())
                ->recordTitleAttribute(ExternalResource::ATTRIBUTE_LINK)
                ->columns(ExternalResourceResource::table($table)->getColumns())
                ->defaultSort(ExternalResource::TABLE . '.' . ExternalResource::ATTRIBUTE_ID, 'desc')
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
            ...ExternalResourceResource::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
            ...ExternalResourceResource::getActions(),
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
            ...ExternalResourceResource::getBulkActions(),
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
            ...ExternalResourceResource::getTableActions(),
        ];
    }
}
