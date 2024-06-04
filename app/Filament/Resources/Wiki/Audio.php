<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Models\Wiki\Audio\AttachAudioToRelatedVideosAction;
use App\Filament\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Filters\TextFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Audio\Pages\CreateAudio;
use App\Filament\Resources\Wiki\Audio\Pages\EditAudio;
use App\Filament\Resources\Wiki\Audio\Pages\ListAudios;
use App\Filament\Resources\Wiki\Audio\Pages\ViewAudio;
use App\Filament\Resources\Wiki\Audio\RelationManagers\VideoAudioRelationManager;
use App\Filament\TableActions\Repositories\Storage\Wiki\Audio\ReconcileAudioTableAction;
use App\Filament\TableActions\Storage\Wiki\Audio\UploadAudioTableAction;
use App\Models\Wiki\Audio as AudioModel;
use App\Models\Wiki\Video;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ActionGroup;
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
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.audios');
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
        return 'audios';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): string
    {
        return AudioModel::ATTRIBUTE_ID;
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
                    ->sortable(),
                    
                TextColumn::make(AudioModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.audio.filename.name'))
                    ->sortable()
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
                Section::make(__('filament.fields.base.file_properties'))
                    ->schema([
                        TextEntry::make(AudioModel::ATTRIBUTE_BASENAME)
                            ->label(__('filament.fields.audio.basename.name')),

                        TextEntry::make(AudioModel::ATTRIBUTE_FILENAME)
                            ->label(__('filament.fields.audio.filename.name')),

                        TextEntry::make(AudioModel::ATTRIBUTE_PATH)
                            ->label(__('filament.fields.audio.path.name')),

                        TextEntry::make(AudioModel::ATTRIBUTE_SIZE)
                            ->label(__('filament.fields.audio.size.name')),

                        TextEntry::make(AudioModel::ATTRIBUTE_MIMETYPE)
                            ->label(__('filament.fields.audio.mimetype.name')),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ]);
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
            RelationGroup::make(static::getLabel(), [
                VideoAudioRelationManager::class,
            ]),
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
            [
                NumberFilter::make(AudioModel::ATTRIBUTE_SIZE)
                    ->labels(__('filament.filters.audio.size_from'), __('filament.filters.audio.size_to'))
                    ->attribute(AudioModel::ATTRIBUTE_SIZE),
            ],
            parent::getFilters(),
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
            [
                ActionGroup::make([
                    MoveAudioAction::make('move-audio')
                        ->label(__('filament.actions.audio.move.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('create', AudioModel::class),
                    
                    DeleteAudioAction::make('delete-audio')
                        ->label(__('filament.actions.audio.delete.name'))
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->authorize('delete', AudioModel::class),

                    AttachAudioToRelatedVideosAction::make('attach-audio-related-video')
                        ->label(__('filament.actions.audio.attach_related_videos.name'))
                        ->requiresConfirmation()
                        ->authorize('update', Video::class),
                ]),
            ],
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
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                UploadAudioTableAction::make('upload-audio')
                    ->label(__('filament.actions.audio.upload.name'))
                    ->icon(__('filament.table_actions.base.upload.icon'))
                    ->requiresConfirmation()
                    ->modalWidth(MaxWidth::FourExtraLarge)
                    ->authorize('create', AudioModel::class),
                    
                ReconcileAudioTableAction::make('reconcile-audio')
                    ->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.audios')]))
                    ->icon(__('filament.table_actions.base.reconcile.icon'))
                    ->requiresConfirmation()
                    ->authorize('create', AudioModel::class),
            ]),
        ];
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
            'view' => ViewAudio::route('/{record:audio_id}'),
            'edit' => EditAudio::route('/{record:audio_id}/edit'),
        ];
    }
}
