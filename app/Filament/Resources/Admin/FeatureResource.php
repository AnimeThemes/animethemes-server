<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Constants\FeatureConstants;
use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Admin\Feature\Pages\ManageFeatures;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\Feature;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeatureResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Feature::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.feature');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.features');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::ADMIN;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedCog6Tooth;
    }

    public static function getRecordSlug(): string
    {
        return 'features';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Feature::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Feature::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.feature.key.name'))
                    ->helperText(__('filament.fields.feature.key.help'))
                    ->readOnly()
                    ->required()
                    ->maxLength(192),

                TextInput::make(Feature::ATTRIBUTE_VALUE)
                    ->label(__('filament.fields.feature.value.name'))
                    ->helperText(__('filament.fields.feature.value.help'))
                    ->required()
                    ->maxLength(192),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl('')
            ->modifyQueryUsing(fn (Builder $query) => $query->where(Feature::ATTRIBUTE_SCOPE, FeatureConstants::NULL_SCOPE))
            ->columns([
                TextColumn::make(Feature::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Feature::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.feature.key.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(Feature::ATTRIBUTE_VALUE)
                    ->label(__('filament.fields.feature.value.name'))
                    ->copyableWithMessage(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Feature::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Feature::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.feature.key.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(Feature::ATTRIBUTE_VALUE)
                            ->label(__('filament.fields.feature.value.name'))
                            ->copyableWithMessage(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ManageFeatures::route('/'),
        ];
    }
}
