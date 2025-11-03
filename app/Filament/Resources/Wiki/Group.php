<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Group\Pages\ListGroups;
use App\Filament\Resources\Wiki\Group\Pages\ViewGroup;
use App\Filament\Resources\Wiki\Group\RelationManagers\ThemeGroupRelationManager;
use App\Models\Wiki\Group as GroupModel;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Group extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = GroupModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.group');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.groups');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedFolderOpen;
    }

    public static function getRecordSlug(): string
    {
        return 'group';
    }

    public static function getRecordTitleAttribute(): string
    {
        return GroupModel::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(GroupModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.group.name.name'))
                    ->helperText(__('filament.fields.group.name.help'))
                    ->required()
                    ->maxLength(192),

                TextInput::make(GroupModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.group.slug.name'))
                    ->helperText(__('filament.fields.group.slug.help'))
                    ->required()
                    ->maxLength(192)
                    ->unique(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(GroupModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(GroupModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.group.name.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(GroupModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.group.slug.name')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(GroupModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(GroupModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.group.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(GroupModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.group.slug.name')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    TextConstraint::make(GroupModel::ATTRIBUTE_NAME)
                        ->label(__('filament.fields.group.name.name')),

                    TextConstraint::make(GroupModel::ATTRIBUTE_SLUG)
                        ->label(__('filament.fields.group.slug.name')),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                ThemeGroupRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListGroups::route('/'),
            'view' => ViewGroup::route('/{record:group_id}'),
        ];
    }
}
