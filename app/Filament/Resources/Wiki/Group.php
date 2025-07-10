<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Group\Pages\ListGroups;
use App\Filament\Resources\Wiki\Group\Pages\ViewGroup;
use App\Filament\Resources\Wiki\Group\RelationManagers\ThemeGroupRelationManager;
use App\Models\Wiki\Group as GroupModel;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Group.
 */
class Group extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = GroupModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
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
    public static function getPluralModelLabel(): string
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
        return __('filament-icons.resources.groups');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'group';
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
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
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

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
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

    /**
     * Get the infolist available for the resource.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
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
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [];
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
            'view' => ViewGroup::route('/{record:group_id}'),
        ];
    }
}
