<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\Wiki\Studio\AttachStudioResourceAction;
use App\Filament\Actions\Models\Wiki\Studio\BackfillStudioAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Studio\Pages\ListStudios;
use App\Filament\Resources\Wiki\Studio\Pages\ViewStudio;
use App\Filament\Resources\Wiki\Studio\RelationManagers\AnimeStudioRelationManager;
use App\Models\Wiki\Studio as StudioModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Studio extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = StudioModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.studio');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.studios');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedBuildingOffice;
    }

    public static function getRecordSlug(): string
    {
        return 'studios';
    }

    public static function getRecordTitleAttribute(): string
    {
        return StudioModel::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(StudioModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->helperText(__('filament.fields.studio.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                Slug::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name'))
                    ->helperText(__('filament.fields.studio.slug.help')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(StudioModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(StudioModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->copyableWithMessage(),

                TextColumn::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name')),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(StudioModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.studio.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(StudioModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.studio.slug.name')),

                        TextEntry::make(StudioModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                AnimeStudioRelationManager::class,
                ResourceRelationManager::class,
                ImageRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            BackfillStudioAction::make(),

            AttachStudioResourceAction::make(),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListStudios::route('/'),
            'view' => ViewStudio::route('/{record:studio_id}'),
        ];
    }
}
