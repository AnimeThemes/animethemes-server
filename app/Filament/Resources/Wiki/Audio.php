<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Repositories\Storage\Wiki\Audio\ReconcileAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\UploadAudioAction;
use App\Filament\BulkActions\Storage\Wiki\Audio\DeleteAudioBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Audio\Pages\ListAudios;
use App\Filament\Resources\Wiki\Audio\Pages\ViewAudio;
use App\Filament\Resources\Wiki\Audio\RelationManagers\VideoAudioRelationManager;
use App\Models\Wiki\Audio as AudioModel;
use Filament\Actions\ActionGroup;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Audio extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = AudioModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.audio');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.audios');
    }

    /**
     * The logical group associated with the resource.
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament-icons.resources.audios');
    }

    /**
     * Get the slug (URI key) for the resource.
     */
    public static function getRecordSlug(): string
    {
        return 'audios';
    }

    /**
     * Get the title attribute for the resource.
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    /**
     * The index page of the resource.
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                VideoAudioRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return \Filament\Tables\Filters\BaseFilter[]
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
     * @return array<int, \Filament\Actions\Action|ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            MoveAudioAction::make(),

            DeleteAudioAction::make(),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array<int, ActionGroup|\Filament\Actions\Action>|null  $actionsIncludedInGroup
     * @return array<int, ActionGroup|\Filament\Actions\Action>
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions([
                DeleteAudioBulkAction::make(),
            ]),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array<int, ActionGroup|\Filament\Actions\Action>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getTableActions(): array
    {
        return [
            UploadAudioAction::make(),

            ActionGroup::make([
                ReconcileAudioAction::make(),
            ]),
        ];
    }

    /**
     * Determine whether the related model can be created.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
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
