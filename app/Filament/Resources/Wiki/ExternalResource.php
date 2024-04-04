<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource\Pages\CreateExternalResource;
use App\Filament\Resources\Wiki\ExternalResource\Pages\EditExternalResource;
use App\Filament\Resources\Wiki\ExternalResource\Pages\ListExternalResources;
use App\Filament\Resources\Wiki\ExternalResource\Pages\ViewExternalResource;
use App\Filament\Resources\Wiki\ExternalResourceResource\RelationManagers\ResourcesRelationManager;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExternalResource extends BaseResource
{
    protected static ?string $model = ExternalResourceModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.external_resource');
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
        return __('filament.resources.label.external_resources');
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationGroup(): ?string
    {
        return __('filament.resources.group.wiki');
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
                TextInput::make(ExternalResourceModel::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.name.name'))
                    ->helperText(__('filament.fields.external_resource.name.help'))
                    ->required(),

                TextInput::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.slug.name'))
                    ->helperText(__('filament.fields.external_resource.slug.help'))
                    ->required(),
            ])
            ->columns(2);
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
                TextColumn::make(ExternalResourceModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.link.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.external_id.name'))
                    ->sortable()
                    ->copyable(),

                SelectColumn::make(ExternalResourceModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_resource.site.name'))
                    ->options(ResourceSite::class)
                    ->sortable(),
            ])
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
        return [
            
        ];
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
        return array_merge(
            parent::getFilters(),
            [

            ]
        );
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
            'index' => ListExternalResources::route('/'),
        //    'create' => CreateExternalResource::route('/create'),
         //   'view' => ViewExternalResource::route('/{record}'),
         //   'edit' => EditExternalResource::route('/{record}/edit'),
        ];
    }
}
