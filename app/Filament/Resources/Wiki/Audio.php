<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Audio\Pages\CreateAudio;
use App\Filament\Resources\Wiki\Audio\Pages\EditAudio;
use App\Filament\Resources\Wiki\Audio\Pages\ListAudios;
use App\Filament\Resources\Wiki\Audio\Pages\ViewAudio;
use App\Models\Wiki\Audio as AudioModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Class Audio.
 */
class Audio extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = AudioModel::class;

    /**
     * The icon displayed to the resource.
     *
     * @var string|null
     */
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
        return __('filament.resources.singularLabel.audio');
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
        return __('filament.resources.label.audios');
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
                TextInput::make(AudioModel::ATTRIBUTE_BASENAME)
                    ->label(__('filament.fields.audio.basename.name'))
                    ->hiddenOn(['create', 'edit']),
                    
                TextInput::make(AudioModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.audio.filename.name'))
                    ->hiddenOn(['create', 'edit']),

                TextInput::make(AudioModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.audio.path.name'))
                    ->hiddenOn(['create', 'edit']),

                TextInput::make(AudioModel::ATTRIBUTE_SIZE)
                    ->label(__('filament.fields.audio.size.name'))
                    ->numeric()
                    ->hiddenOn(['create', 'edit']),

                TextInput::make(AudioModel::ATTRIBUTE_MIMETYPE)
                    ->label(__('filament.fields.audio.mimetype.name'))
                    ->hiddenOn(['create', 'edit']),
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
                TextColumn::make(AudioModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(AudioModel::ATTRIBUTE_BASENAME)
                    ->label(__('filament.fields.audio.basename.name'))
                    ->copyable()
                    ->hidden(),
                    
                TextColumn::make(AudioModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.audio.filename.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(AudioModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.audio.path.name'))
                    ->sortable()
                    ->copyable()
                    ->hidden(),

                TextColumn::make(AudioModel::ATTRIBUTE_SIZE)
                    ->label(__('filament.fields.audio.size.name'))
                    ->numeric()
                    ->sortable()
                    ->copyable()
                    ->hidden(),

                TextColumn::make(AudioModel::ATTRIBUTE_MIMETYPE)
                    ->label(__('filament.fields.audio.mimetype.name'))
                    ->sortable()
                    ->copyable()
                    ->hidden(),
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
        return array_merge(
            parent::getFilters(),
            []
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
            'index' => ListAudios::route('/'),
            'create' => CreateAudio::route('/create'),
            'view' => ViewAudio::route('/{record}'),
            'edit' => EditAudio::route('/{record}/edit'),
        ];
    }
}
