<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Actions\Models\Wiki\Image\UploadImageAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Image\Pages\ListImages;
use App\Filament\Resources\Wiki\Image\Pages\ViewImage;
use App\Filament\Resources\Wiki\Image\RelationManagers\AnimeImageRelationManager;
use App\Filament\Resources\Wiki\Image\RelationManagers\ArtistImageRelationManager;
use App\Filament\Resources\Wiki\Image\RelationManagers\PlaylistImageRelationManager;
use App\Filament\Resources\Wiki\Image\RelationManagers\StudioImageRelationManager;
use App\Models\Wiki\Image as ImageModel;
use Filament\Infolists\Components\ImageEntry;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Image extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ImageModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.image');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.images');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedPhoto;
    }

    public static function getRecordSlug(): string
    {
        return 'images';
    }

    public static function getRecordTitleAttribute(): string
    {
        return ImageModel::ATTRIBUTE_PATH;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make(ImageModel::ATTRIBUTE_FACET)
                    ->label(__('filament.fields.image.facet.name'))
                    ->helperText(__('filament.fields.image.facet.help'))
                    ->options(ImageFacet::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                Stack::make([
                    TextColumn::make(ImageModel::ATTRIBUTE_ID)
                        ->label(__('filament.fields.base.id')),

                    TextColumn::make(ImageModel::ATTRIBUTE_FACET)
                        ->label(__('filament.fields.image.facet.name'))
                        ->formatStateUsing(fn (ImageFacet $state): ?string => $state->localize()),

                    ImageColumn::make(ImageModel::ATTRIBUTE_PATH)
                        ->label(__('filament.fields.image.image.name'))
                        ->disk(Config::get('image.disk'))
                        ->imageWidth(100)
                        ->imageHeight(150),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(ImageModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(ImageModel::ATTRIBUTE_FACET)
                            ->label(__('filament.fields.image.facet.name'))
                            ->formatStateUsing(fn (ImageFacet $state): ?string => $state->localize()),

                        ImageEntry::make(ImageModel::ATTRIBUTE_PATH)
                            ->label(__('filament.fields.image.image.name'))
                            ->disk(Config::get('image.disk'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

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
                    SelectConstraint::make(ImageModel::ATTRIBUTE_FACET)
                        ->label(__('filament.fields.image.facet.name'))
                        ->options(ImageFacet::class),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\ActionGroup|\Filament\Actions\Action>
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),

            UploadImageAction::make(),
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
                AnimeImageRelationManager::class,
                ArtistImageRelationManager::class,
                PlaylistImageRelationManager::class,
                StudioImageRelationManager::class,

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
            'index' => ListImages::route('/'),
            'view' => ViewImage::route('/{record:image_id}'),
        ];
    }
}
