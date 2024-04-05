<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResourcesRelationManager extends RelationManager
{
    protected static string $relationship = Anime::RELATION_RESOURCES;

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(ExternalResource::ATTRIBUTE_SITE)
                    ->label(__('nova.fields.external_resource.site.name'))
                    ->helperText(__('nova.fields.external_resource.site.help'))
                    ->options(ResourceSite::asSelectArray())
                    ->required(),

                TextInput::make(ExternalResource::ATTRIBUTE_LINK)
                    ->label(__('nova.fields.external_resource.link.name'))
                    ->helperText(__('nova.fields.external_resource.link.help'))
                    ->required()
                    ->url()
                    ->maxLength(255),

                TextInput::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('nova.fields.anime.resources.as.name'))
                    ->helperText(__('nova.fields.anime.resources.as.help')),
            ]);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(ExternalResource::ATTRIBUTE_LINK)
            ->columns([
                TextColumn::make(ExternalResource::ATTRIBUTE_ID)
                    ->label(__('nova.fields.base.id'))
                    ->numeric(),

                TextColumn::make(ExternalResource::ATTRIBUTE_LINK)
                    ->label(__('nova.fields.external_resource.link.name')),

                TextColumn::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('nova.fields.anime.resources.as.name')),

                SelectColumn::make(ExternalResource::ATTRIBUTE_SITE)
                    ->label(__('nova.fields.external_resource.site.name'))
                    ->options(ResourceSite::asSelectArray()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
