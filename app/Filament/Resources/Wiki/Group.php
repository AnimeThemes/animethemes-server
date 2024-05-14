<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Group\Pages\CreateGroup;
use App\Filament\Resources\Wiki\Group\Pages\EditGroup;
use App\Filament\Resources\Wiki\Group\Pages\ListGroups;
use App\Filament\Resources\Wiki\Group\Pages\ViewGroup;
use App\Filament\Resources\Wiki\Group\RelationManagers\ThemeGroupRelationManager;
use App\Models\Wiki\Group as GroupModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

/**
 * Class Group.
 */
class Group extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = GroupModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.group');
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
        return __('filament.resources.label.groups');
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
        return __('filament.resources.group.wiki');
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
        return __('filament.resources.icon.groups');
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
        return static::getDefaultSlug().'group';
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
        return GroupModel::ATTRIBUTE_NAME;
    }

    /**
     * Get the route key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): string
    {
        return GroupModel::ATTRIBUTE_ID;
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
                TextInput::make(GroupModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.group.name.name'))
                    ->helperText(__('filament.fields.group.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),

                TextInput::make(GroupModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.group.slug.name'))
                    ->helperText(__('filament.fields.group.slug.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192', 'alpha_dash', Rule::unique(GroupModel::class, GroupModel::ATTRIBUTE_SLUG)->__toString()]),
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
                TextColumn::make(GroupModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(GroupModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.group.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make(GroupModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.group.slug.name'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort(GroupModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
        return [
            RelationGroup::make(static::getLabel(), [
                ThemeGroupRelationManager::class,
            ]),
        ];
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
            [],
            parent::getFilters(),
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
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListGroups::route('/'),
            'create' => CreateGroup::route('/create'),
            'view' => ViewGroup::route('/{record:group_id}'),
            'edit' => EditGroup::route('/{record:group_id}/edit'),
        ];
    }
}
