<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Base;

use App\Actions\Storage\Base\UploadAction as BaseUploadAction;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Video\RelationManagers\ScriptVideoRelationManager;
use App\Filament\TableActions\Storage\StorageTableAction;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Storage;

/**
 * Class UploadTableAction.
 */
abstract class UploadTableAction extends StorageTableAction implements InteractsWithDisk
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
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        $fs = Storage::disk($this->disk());

        return $form->schema([
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
                ->rules(['doesnt_start_with:/', 'doesnt_end_with:/', 'string', new StorageDirectoryExistsRule($fs)])
                ->hidden(fn ($livewire) => $livewire instanceof VideoEntryRelationManager || $livewire instanceof ScriptVideoRelationManager),
        ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array  $fields
     * @return BaseUploadAction
     */
    abstract protected function storageAction(array $fields): BaseUploadAction;

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    abstract protected function fileRules(): array;
}
