<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\UploadAction as BaseUploadAction;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\Actions\Storage\StorageAction;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\ScriptVideoRelationManager;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class UploadAction.
 */
abstract class UploadAction extends StorageAction implements InteractsWithDisk
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(__('filament-icons.table_actions.base.upload'));
    }

    /**
     * Get the schema available on the action.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
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
     * @param  Model|null  $record
     * @param  array<string, mixed>  $data
     * @return BaseUploadAction
     */
    abstract protected function storageAction(?Model $record, array $data): BaseUploadAction;

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    abstract protected function fileRules(): array;
}
