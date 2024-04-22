<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\Feature\Pages\CreateFeature;
use App\Filament\Resources\Admin\Feature\Pages\EditFeature;
use App\Filament\Resources\Admin\Feature\Pages\ListFeatures;
use App\Filament\Resources\Admin\Feature\Pages\ViewFeature;
use App\Models\Admin\Feature as FeatureModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Class Feature.
 */
class Feature extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = FeatureModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
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
    public static function getPluralLabel(): string
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
        return __('filament.resources.icon.features');
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
        return 'features';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return FeatureModel::ATTRIBUTE_ID;
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
                TextInput::make(FeatureModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.feature.key.name'))
                    ->helperText(__('filament.fields.feature.key.help'))
                    ->readOnly()
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),

                TextInput::make(FeatureModel::ATTRIBUTE_VALUE)
                    ->label(__('filament.fields.feature.value.name'))
                    ->helperText(__('filament.fields.feature.value.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),
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
                TextColumn::make(FeatureModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(FeatureModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.feature.key.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(FeatureModel::ATTRIBUTE_VALUE)
                    ->label(__('filament.fields.feature.value.name'))
                    ->sortable()
                    ->copyable(),
            ])
            ->defaultSort(FeatureModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
        return [];
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
            'index' => ListFeatures::route('/'),
            'create' => CreateFeature::route('/create'),
            'view' => ViewFeature::route('/{record:feature_id}'),
            'edit' => EditFeature::route('/{record:feature_id}/edit'),
        ];
    }
}
