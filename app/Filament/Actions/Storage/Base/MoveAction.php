<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\MoveAction as BaseMoveAction;
use App\Contracts\Actions\Storage\StorageAction as StorageActionContract;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\Actions\Storage\StorageAction;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class MoveAction.
 */
abstract class MoveAction extends StorageAction implements InteractsWithDisk
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(__('filament-icons.actions.storage.move'));
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
     * @param  Model  $record
     * @param  array<string, mixed>  $data
     * @return BaseMoveAction
     */
    abstract protected function storageAction(?Model $record, array $data): StorageActionContract;

    /**
     * Resolve the default value for the path field.
     *
     * @return string|null
     */
    abstract protected function defaultPath(): ?string;

    /**
     * The file extension that the path must end with.
     *
     * @return string
     */
    abstract protected function allowedFileExtension(): string;
}
