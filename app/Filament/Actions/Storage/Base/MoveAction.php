<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\MoveAction as BaseMoveAction;
use App\Contracts\Actions\Storage\StorageAction as StorageActionContract;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\Actions\Storage\StorageAction;
use App\Filament\Components\Fields\TextInput;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

abstract class MoveAction extends StorageAction implements InteractsWithDisk
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::OutlinedArrowLongRight);
    }

    public function getSchema(Schema $schema): Schema
    {
        $defaultPath = $this->defaultPath();

        $fs = Storage::disk($this->disk());

        return $schema
            ->components([
                TextInput::make('path')
                    ->label(__('filament.actions.storage.move.fields.path.name'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->required()
                    ->doesntStartWith('/')
                    ->endsWith($this->allowedFileExtension())
                    ->rule(new StorageFileDirectoryExistsRule($fs))
                    ->default($defaultPath),
            ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     * @return BaseMoveAction
     */
    abstract protected function storageAction(?Model $record, array $data): StorageActionContract;

    /**
     * Resolve the default value for the path field.
     */
    abstract protected function defaultPath(): ?string;

    /**
     * The file extension that the path must end with.
     */
    abstract protected function allowedFileExtension(): string;
}
