<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Filament\BulkActions\Storage\Wiki\Audio\DeleteAudioBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Audio\Pages\ListAudios;
use App\Filament\Resources\Wiki\Audio\Pages\ViewAudio;
use App\Filament\Resources\Wiki\Audio\RelationManagers\VideoAudioRelationManager;
use App\Filament\TableActions\Repositories\Storage\Wiki\Audio\ReconcileAudioTableAction;
use App\Filament\TableActions\Storage\Wiki\Audio\UploadAudioTableAction;
use App\Models\Wiki\Audio as AudioModel;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
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
        return __('filament-icons.resources.audios');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'audios';
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
        return AudioModel::ATTRIBUTE_BASENAME;
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
        return $form;
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
            ->columns([
                TextColumn::make(AudioModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(AudioModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.audio.filename.name'))
                    ->searchable()
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

                TimestampSection::make(),
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

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            NumberFilter::make(AudioModel::ATTRIBUTE_SIZE)
                ->label(__('filament.fields.audio.size.name')),

            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),

            ActionGroup::make([
                MoveAudioAction::make('move-audio'),

                DeleteAudioAction::make('delete-audio'),
            ]),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),

            DeleteAudioBulkAction::make('delete-audio'),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getTableActions(): array
    {
        return [
            ActionGroup::make([
                UploadAudioTableAction::make('upload-audio'),

                ReconcileAudioTableAction::make('reconcile-audio'),
            ]),
        ];
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    public static function canCreate(): bool
    {
        return false;
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
            'view' => ViewAudio::route('/{record:audio_id}'),
        ];
    }
}
