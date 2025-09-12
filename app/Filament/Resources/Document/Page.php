<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Document\Page\Pages\ListPages;
use App\Filament\Resources\Document\Page\Pages\ViewPage;
use App\Models\Document\Page as PageModel;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Page extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = PageModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.pages');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::DOCUMENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedDocumentText;
    }

    public static function getRecordSlug(): string
    {
        return 'pages';
    }

    public static function getRecordTitleAttribute(): string
    {
        return PageModel::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(PageModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.page.name.name'))
                    ->helperText(__('filament.fields.page.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                TextInput::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->helperText(__('filament.fields.page.slug.help'))
                    ->regex('/^[\pL\pM\pN\/_-]+$/u'),

                MarkdownEditor::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
                    ->helperText(__('filament.fields.page.body.help'))
                    ->required()
                    ->maxLength(16777215)
                    ->columnSpan(2)
                    ->formatStateUsing(fn (?PageModel $record) => $record?->body),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(PageModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(PageModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.page.name.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->copyableWithMessage(),

                TextColumn::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
                    ->hidden(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(PageModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(PageModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.page.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(PageModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.page.slug.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(PageModel::ATTRIBUTE_BODY)
                            ->label(__('filament.fields.page.body.name'))
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

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
            'index' => ListPages::route('/'),
            'view' => ViewPage::route('/{record:page_id}'),
        ];
    }
}
