<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\Dump\Pages\CreateDump;
use App\Filament\Resources\Admin\Dump\Pages\EditDump;
use App\Filament\Resources\Admin\Dump\Pages\ListDumps;
use App\Filament\Resources\Admin\Dump\Pages\ViewDump;
use App\Filament\TableActions\Repositories\Storage\Admin\Dump\ReconcileDumpTableAction;
use App\Filament\TableActions\Storage\Admin\DumpDocumentTableAction;
use App\Filament\TableActions\Storage\Admin\DumpWikiTableAction;
use App\Filament\TableActions\Storage\Admin\PruneDumpTableAction;
use App\Models\Admin\Dump as DumpModel;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Class Dump.
 */
class Dump extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = DumpModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.dump');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralLabel(): string
    {
        return __('filament.resources.label.dumps');
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.admin');
    }

    /**
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.dumps');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'dumps';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return DumpModel::ATTRIBUTE_ID;
    }

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(DumpModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.dump.path'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192'])
                    ->hiddenOn(['create', 'edit']),
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(DumpModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(DumpModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.dump.path'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),
            ])
            ->defaultSort(DumpModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions())
            ->headerActions(static::getHeaderActions());
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Infolist  $infolist
     * @return Infolist
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps()),
            ]);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            []
        );
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                DumpWikiTableAction::make('dump-wiki')
                    ->label(__('filament.actions.dump.dump.name.wiki'))
                    ->requiresConfirmation()
                    ->authorize('create', DumpModel::class),

                DumpDocumentTableAction::make('dump-document')
                    ->label(__('filament.actions.dump.dump.name.document'))
                    ->requiresConfirmation()
                    ->authorize('create', DumpModel::class),
                
                PruneDumpTableAction::make('prune-dump')
                    ->label(__('filament.actions.dump.prune.name'))
                    ->requiresConfirmation(),

                ReconcileDumpTableAction::make('reconcile-dump')
                    ->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.dumps')]))
                    ->requiresConfirmation()
                    ->authorize('create', DumpModel::class),
            ]),
        ];
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListDumps::route('/'),
            'create' => CreateDump::route('/create'),
            'view' => ViewDump::route('/{record:dump_id}'),
            'edit' => EditDump::route('/{record:dump_id}/edit'),
        ];
    }
}