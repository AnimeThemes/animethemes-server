<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Series\Pages\ListSeries;
use App\Filament\Resources\Wiki\Series\Pages\ViewSeries;
use App\Filament\Resources\Wiki\Series\RelationManagers\AnimeSeriesRelationManager;
use App\Models\Wiki\Series as SeriesModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Series extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = SeriesModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.series');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.series');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.wiki');
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedFolder;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'series';
    }

    public static function getRecordTitleAttribute(): string
    {
        return SeriesModel::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SeriesModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.series.name.name'))
                    ->helperText(__('filament.fields.series.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                Slug::make(SeriesModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.series.slug.name'))
                    ->helperText(__('filament.fields.series.slug.help')),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(SeriesModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(SeriesModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.series.name.name'))
                    ->copyableWithMessage(),

                TextColumn::make(SeriesModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.series.slug.name')),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(SeriesModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(SeriesModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.series.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(SeriesModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.series.slug.name')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                AnimeSeriesRelationManager::class,

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
            'index' => ListSeries::route('/'),
            'view' => ViewSeries::route('/{record:series_id}'),
        ];
    }
}
