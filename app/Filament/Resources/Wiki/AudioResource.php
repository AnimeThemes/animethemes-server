<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Repositories\Storage\Wiki\Audio\ReconcileAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Filament\Actions\Storage\Wiki\Audio\UploadAudioAction;
use App\Filament\BulkActions\Storage\Wiki\Audio\DeleteAudioBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Audio\Pages\ListAudios;
use App\Filament\Resources\Wiki\Audio\Pages\ViewAudio;
use App\Filament\Resources\Wiki\Audio\RelationManagers\VideoAudioRelationManager;
use App\Models\Wiki\Audio;
use Filament\Actions\ActionGroup;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AudioResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Audio::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.audio');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.audios');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedSpeakerWave;
    }

    public static function getRecordSlug(): string
    {
        return 'audios';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Audio::ATTRIBUTE_BASENAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Audio::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Audio::ATTRIBUTE_FILENAME)
                    ->label(__('filament.fields.audio.filename.name'))
                    ->searchable()
                    ->copyableWithMessage(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.fields.base.file_properties'))
                    ->schema([
                        TextEntry::make(Audio::ATTRIBUTE_BASENAME)
                            ->label(__('filament.fields.audio.basename.name')),

                        TextEntry::make(Audio::ATTRIBUTE_FILENAME)
                            ->label(__('filament.fields.audio.filename.name')),

                        TextEntry::make(Audio::ATTRIBUTE_PATH)
                            ->label(__('filament.fields.audio.path.name')),

                        TextEntry::make(Audio::ATTRIBUTE_SIZE)
                            ->label(__('filament.fields.audio.size.name')),

                        TextEntry::make(Audio::ATTRIBUTE_MIMETYPE)
                            ->label(__('filament.fields.audio.mimetype.name')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    NumberConstraint::make(Audio::ATTRIBUTE_SIZE)
                        ->label(__('filament.fields.audio.size.name')),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
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
     * @return array<int, ActionGroup|\Filament\Actions\Action>
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

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
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
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAudios::route('/'),
            'view' => ViewAudio::route('/{record:audio_id}'),
        ];
    }
}
