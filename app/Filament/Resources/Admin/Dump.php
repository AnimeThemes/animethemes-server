<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\Dump\Pages\ManageDumps;
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
        return __('filament-icons.resources.dumps');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'dumps';
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return DumpModel::ATTRIBUTE_PATH;
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
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl('')
            ->columns([
                TextColumn::make(DumpModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(DumpModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.dump.path'))
                    ->searchable()
                    ->copyableWithMessage(),
            ]);
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
                Section::make(static::getRecordTitle($infolist->getRecord()))
                    ->schema([
                        TextEntry::make(DumpModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(DumpModel::ATTRIBUTE_PATH)
                            ->label(__('filament.fields.dump.path'))
                            ->copyableWithMessage(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getTableActions(): array
    {
        return [
            ActionGroup::make([
                DumpWikiTableAction::make('dump-wiki'),

                DumpDocumentTableAction::make('dump-document'),

                PruneDumpTableAction::make('prune-dump'),

                ReconcileDumpTableAction::make('reconcile-dump'),
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
            'index' => ManageDumps::route('/'),
        ];
    }
}
