<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

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
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Class Image.
 */
class Image extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ImageModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.image');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.images');
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
        return __('filament-icons.resources.images');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'images';
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
        return ImageModel::ATTRIBUTE_PATH;
    }

    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
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
                Stack::make([
                    TextColumn::make(ImageModel::ATTRIBUTE_ID)
                        ->label(__('filament.fields.base.id')),

                    TextColumn::make(ImageModel::ATTRIBUTE_FACET)
                        ->label(__('filament.fields.image.facet.name'))
                        ->formatStateUsing(fn (ImageFacet $state) => $state->localize()),

                    ImageColumn::make(ImageModel::ATTRIBUTE_PATH)
                        ->label(__('filament.fields.image.image.name'))
                        ->disk(Config::get('image.disk'))
                        ->width(100)
                        ->height(150),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
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
                            ->formatStateUsing(fn (ImageFacet $state) => $state->localize()),

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
                AnimeImageRelationManager::class,
                ArtistImageRelationManager::class,
                PlaylistImageRelationManager::class,
                StudioImageRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Filament\Tables\Filters\BaseFilter>
     */
    public static function getFilters(): array
    {
        return [
            SelectFilter::make(ImageModel::ATTRIBUTE_FACET)
                ->label(__('filament.fields.image.facet.name'))
                ->options(ImageFacet::class),

            ...parent::getFilters(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array<int, \Filament\Actions\ActionGroup|\Filament\Actions\Action>
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),

            UploadImageAction::make(),
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
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListImages::route('/'),
            'view' => ViewImage::route('/{record:image_id}'),
        ];
    }
}
