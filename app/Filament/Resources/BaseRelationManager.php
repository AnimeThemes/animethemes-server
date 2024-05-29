<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Components\Fields\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class BaseRelationManager.
 */
abstract class BaseRelationManager extends RelationManager
{
    /**
     * The index page of the relation resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (BaseResource $record): string => $record::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
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
            TrashedFilter::make()
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('filament.actions.base.view')),

            EditAction::make()
                ->label(__('filament.actions.base.edit')),

            DetachAction::make()
                ->label(__('filament.actions.base.detach')),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->label(__('filament.bulk_actions.base.delete')),

                ForceDeleteBulkAction::make()
                    ->label(__('filament.bulk_actions.base.forcedelete')),

                RestoreBulkAction::make()
                    ->label(__('filament.bulk_actions.base.restore')),

                DetachBulkAction::make()
                    ->label(__('filament.bulk_actions.base.detach')),
            ]),
        ];
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            AttachAction::make()
                ->hidden(fn (BaseRelationManager $livewire) => !($livewire->getRelationship() instanceof BelongsToMany))
                ->recordSelect(function (BaseRelationManager $livewire) {
                    /** @var string */
                    $model = $livewire->getTable()->getModel();
                    $title = $livewire->getTable()->getRecordTitle(new $model);
                    return Select::make('recordId')
                        ->label($title)
                        ->useScout($model);
                }),
        ];
    }
}