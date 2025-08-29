<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Admin\Announcement\Pages\ManageAnnouncements;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\Announcement as AnnouncementModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Announcement extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = AnnouncementModel::class;

    /**
     * Get the displayable singular label of the resource.
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.announcement');
    }

    /**
     * Get the displayable label of the resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.announcements');
    }

    /**
     * The logical group associated with the resource.
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.admin');
    }

    /**
     * The icon displayed to the resource.
     */
    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedMegaphone;
    }

    /**
     * Get the slug (URI key) for the resource.
     */
    public static function getRecordSlug(): string
    {
        return 'announcements';
    }

    /**
     * The form to the actions.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Checkbox::make(AnnouncementModel::ATTRIBUTE_PUBLIC)
                    ->label(__('filament.fields.announcement.public.name'))
                    ->helperText(__('filament.fields.announcement.public.help')),

                MarkdownEditor::make(AnnouncementModel::ATTRIBUTE_CONTENT)
                    ->label(__('filament.fields.announcement.content'))
                    ->required()
                    ->maxLength(65535),
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl('')
            ->columns([
                TextColumn::make(AnnouncementModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                IconColumn::make(AnnouncementModel::ATTRIBUTE_PUBLIC)
                    ->label(__('filament.fields.announcement.public.name'))
                    ->boolean(),

                TextColumn::make(AnnouncementModel::ATTRIBUTE_CONTENT)
                    ->label(__('filament.fields.announcement.content'))
                    ->searchable()
                    ->copyableWithMessage(),
            ]);
    }

    /**
     * Get the infolist available for the resource.
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(AnnouncementModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(AnnouncementModel::ATTRIBUTE_PUBLIC)
                            ->label(__('filament.fields.announcement.public.name')),

                        TextEntry::make(AnnouncementModel::ATTRIBUTE_CONTENT)
                            ->label(__('filament.fields.announcement.content'))
                            ->markdown()
                            ->columnSpanFull()
                            ->copyableWithMessage(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ManageAnnouncements::route('/'),
        ];
    }
}
