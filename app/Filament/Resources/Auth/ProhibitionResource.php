<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\Prohibition\Pages\ListProhibitions;
use App\Filament\Resources\Auth\Prohibition\Pages\ViewProhibition;
use App\Filament\Resources\Auth\Prohibition\RelationManagers\SanctionProhibitionRelationManager;
use App\Filament\Resources\Auth\Prohibition\RelationManagers\UserProhibitionRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Prohibition;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProhibitionResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Prohibition::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.prohibition');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.prohibitions');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::AUTH;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedExclamationTriangle;
    }

    public static function getRecordSlug(): string
    {
        return 'prohibitions';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Prohibition::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Prohibition::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.prohibition.name'))
                    ->required()
                    ->maxLength(192)
                    ->disabled(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl(fn (Prohibition $record): string => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make(Prohibition::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Prohibition::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.prohibition.name'))
                    ->searchable()
                    ->copyableWithMessage(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Prohibition::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Prohibition::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.prohibition.name'))
                            ->copyableWithMessage(),
                    ]),

                TimestampSection::make(),
            ])
            ->columns(2);
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                SanctionProhibitionRelationManager::class,
                UserProhibitionRelationManager::class,
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListProhibitions::route('/'),
            'view' => ViewProhibition::route('/{record:id}'),
        ];
    }
}
