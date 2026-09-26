<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Theme\Pages\ViewTheme;
use App\Filament\Resources\Wiki\Theme\RelationManagers\ThemeStaffThemeRelationManager;
use App\Filament\Resources\Wiki\ThemeStaff\Pages\ListThemeStaff;
use App\Filament\Resources\Wiki\ThemeStaff\Pages\ViewThemeStaff;
use App\Models\Wiki\ThemeStaff;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ThemeStaffResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ThemeStaff::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.theme_staff');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.theme_staff');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedListBullet;
    }

    public static function getRecordTitleAttribute(): string
    {
        return ThemeStaff::ATTRIBUTE_ID;
    }

    public static function getRecordSlug(): string
    {
        return 'theme-staff';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        /** @phpstan-ignore-next-line */
        return $query->with([
            ThemeStaff::RELATION_ARTIST,
            ThemeStaff::RELATION_THEME,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(ThemeStaff::ATTRIBUTE_ARTIST)
                    ->resource(ArtistResource::class)
                    ->showCreateOption()
                    ->required(),

                TextInput::make(ThemeStaff::ATTRIBUTE_ROLE)
                    ->label(__('filament.fields.theme_staff.role.name'))
                    ->helperText(__('filament.fields.theme_staff.role.help'))
                    ->required()
                    ->datalist(ThemeStaff::$roles),

                TextInput::make(ThemeStaff::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.theme_staff.alias.name'))
                    ->helperText(__('filament.fields.theme_staff.alias.help')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(ThemeStaff::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(ThemeStaff::RELATION_THEME, ThemeResource::class)
                    ->hiddenOn(ThemeStaffThemeRelationManager::class),

                BelongsToColumn::make(ThemeStaff::RELATION_ARTIST, ArtistResource::class),

                TextColumn::make(ThemeStaff::ATTRIBUTE_ROLE)
                    ->label(__('filament.fields.theme_staff.role.name')),

                TextColumn::make(ThemeStaff::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.theme_staff.alias.name')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(ThemeStaff::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(ThemeStaff::RELATION_THEME, SongResource::class)
                            ->hiddenOn(ViewTheme::class),

                        BelongsToEntry::make(ThemeStaff::RELATION_ARTIST, ArtistResource::class),

                        TextEntry::make(ThemeStaff::ATTRIBUTE_ROLE)
                            ->label(__('filament.fields.theme_staff.role.name')),

                        TextEntry::make(ThemeStaff::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.theme_staff.alias.name')),
                    ])
                    ->columns(fn ($livewire): int => $livewire instanceof ViewTheme ? 4 : 2),

                TimestampSection::make()
                    ->hiddenOn(ViewTheme::class),
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
                    TextConstraint::make(ThemeStaff::ATTRIBUTE_ROLE)
                        ->label(__('filament.fields.theme_staff.role.name')),

                    TextConstraint::make(ThemeStaff::ATTRIBUTE_ALIAS)
                        ->label(__('filament.fields.theme_staff.alias.name')),

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
            'index' => ListThemeStaff::route('/'),
            'view' => ViewThemeStaff::route('/{record:id}'),
        ];
    }
}
