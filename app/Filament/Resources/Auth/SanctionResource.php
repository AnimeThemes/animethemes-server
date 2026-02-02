<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\Auth\Sanction\GiveProhibitionAction;
use App\Filament\Actions\Models\Auth\Sanction\RevokeProhibitionAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\Sanction\Pages\ListSanctions;
use App\Filament\Resources\Auth\Sanction\Pages\ViewSanction;
use App\Filament\Resources\Auth\Sanction\RelationManagers\ProhibitionSanctionRelationManager;
use App\Filament\Resources\Auth\Sanction\RelationManagers\UserSanctionRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Sanction;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SanctionResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Sanction::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.sanction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.sanctions');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::AUTH;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedShieldExclamation;
    }

    public static function getRecordSlug(): string
    {
        return 'sanctions';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Sanction::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Sanction::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.sanction.name'))
                    ->required()
                    ->maxLength(192),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Sanction::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Sanction::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.sanction.name'))
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
                        TextEntry::make(Sanction::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Sanction::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.sanction.name'))
                            ->copyableWithMessage(),
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
                ProhibitionSanctionRelationManager::class,
                UserSanctionRelationManager::class,
            ]),
        ];
    }

    /**
     * @return array<int, \Filament\Tables\Filters\BaseFilter>
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            GiveProhibitionAction::make(),

            RevokeProhibitionAction::make(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSanctions::route('/'),
            'view' => ViewSanction::route('/{record:id}'),
        ];
    }
}
