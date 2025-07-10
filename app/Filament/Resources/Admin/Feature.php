<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Constants\FeatureConstants;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Admin\Feature\Pages\ManageFeatures;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\Feature as FeatureModel;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Feature.
 */
class Feature extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = FeatureModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.feature');
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
        return __('filament.resources.label.features');
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
        return __('filament-icons.resources.features');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'features';
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
        return FeatureModel::ATTRIBUTE_NAME;
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
                TextInput::make(FeatureModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.feature.key.name'))
                    ->helperText(__('filament.fields.feature.key.help'))
                    ->readOnly()
                    ->required()
                    ->maxLength(192),

                TextInput::make(FeatureModel::ATTRIBUTE_VALUE)
                    ->label(__('filament.fields.feature.value.name'))
                    ->helperText(__('filament.fields.feature.value.help'))
                    ->required()
                    ->maxLength(192),
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
            ->modifyQueryUsing(fn (Builder $query) => $query->where(FeatureModel::ATTRIBUTE_SCOPE, FeatureConstants::NULL_SCOPE))
            ->columns([
                TextColumn::make(FeatureModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(FeatureModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.feature.key.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(FeatureModel::ATTRIBUTE_VALUE)
                    ->label(__('filament.fields.feature.value.name'))
                    ->copyableWithMessage(),
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
                        TextEntry::make(FeatureModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(FeatureModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.feature.key.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(FeatureModel::ATTRIBUTE_VALUE)
                            ->label(__('filament.fields.feature.value.name'))
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
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
            'index' => ManageFeatures::route('/'),
        ];
    }
}
