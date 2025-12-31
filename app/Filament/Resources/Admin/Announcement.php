<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Admin\Announcement\Pages\ManageAnnouncements;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\Announcement as AnnouncementModel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.announcement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.announcements');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::ADMIN;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedMegaphone;
    }

    public static function getRecordSlug(): string
    {
        return 'announcements';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make(AnnouncementModel::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.announcement.start_at.name'))
                    ->helperText(__('filament.fields.announcement.start_at.help'))
                    ->native(false)
                    ->required()
                    ->before(AnnouncementModel::ATTRIBUTE_END_AT),

                DatePicker::make(AnnouncementModel::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.announcement.end_at.name'))
                    ->helperText(__('filament.fields.announcement.end_at.help'))
                    ->native(false)
                    ->required()
                    ->after(AnnouncementModel::ATTRIBUTE_START_AT),

                MarkdownEditor::make(AnnouncementModel::ATTRIBUTE_CONTENT)
                    ->label(__('filament.fields.announcement.content'))
                    ->required()
                    ->maxLength(65535),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl('')
            ->columns([
                TextColumn::make(AnnouncementModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(AnnouncementModel::ATTRIBUTE_START_AT)
                    ->label(__('filament.fields.announcement.start_at.name'))
                    ->date(),

                TextColumn::make(AnnouncementModel::ATTRIBUTE_END_AT)
                    ->label(__('filament.fields.announcement.end_at.name'))
                    ->date(),

                TextColumn::make(AnnouncementModel::ATTRIBUTE_CONTENT)
                    ->label(__('filament.fields.announcement.content'))
                    ->searchable()
                    ->copyableWithMessage(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(AnnouncementModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(AnnouncementModel::ATTRIBUTE_START_AT)
                            ->label(__('filament.fields.announcement.start_at.name'))
                            ->date(),

                        TextEntry::make(AnnouncementModel::ATTRIBUTE_END_AT)
                            ->label(__('filament.fields.announcement.end_at.name'))
                            ->date(),

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
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ManageAnnouncements::route('/'),
        ];
    }
}
