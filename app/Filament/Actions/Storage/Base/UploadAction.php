<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\UploadAction as BaseUploadAction;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\Actions\Storage\StorageAction;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\ScriptVideoRelationManager;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

abstract class UploadAction extends StorageAction implements InteractsWithDisk
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::OutlinedArrowUpTray);
    }

    public function getSchema(Schema $schema): Schema
    {
        $fs = Storage::disk($this->disk());

        return $schema
            ->components([
                FileUpload::make('file')
                    ->label(__('filament.actions.storage.upload.fields.file.name'))
                    ->helperText(__('filament.actions.storage.upload.fields.file.help'))
                    ->required()
                    ->live(true)
                    ->rules($this->fileRules())
                    ->preserveFilenames()
                    ->storeFiles(false),

                TextInput::make('path')
                    ->label(__('filament.actions.storage.upload.fields.path.name'))
                    ->helperText(__('filament.actions.storage.upload.fields.path.help'))
                    ->doesntStartWith('/')
                    ->doesntEndWith('/')
                    ->rule(new StorageDirectoryExistsRule($fs))
                    ->hidden(fn ($livewire) => $livewire instanceof VideoEntryRelationManager || $livewire instanceof ScriptVideoRelationManager),
            ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    abstract protected function storageAction(?Model $record, array $data): BaseUploadAction;

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    abstract protected function fileRules(): array;
}
