<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Constants\FeatureConstants;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\Feature\Pages\ManageFeatures;
use App\Models\Admin\Feature as FeatureModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        return __('filament-icons.resources.features');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     * @param  Infolist  $infolist
     * @return Infolist
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(static::getRecordTitle($infolist->getRecord()))
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

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
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
     * Get the actions available for the resource.
     *
     * @return array
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
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return array_merge(
            parent::getTableActions(),
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
            'index' => ManageFeatures::route('/'),
        ];
    }
}
