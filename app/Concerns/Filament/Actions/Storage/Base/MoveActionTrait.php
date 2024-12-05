<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\MoveAction as BaseMoveAction;
use App\Contracts\Actions\Storage\StorageAction;
use App\Models\BaseModel;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Storage;

/**
 * Trait MoveActionTrait.
 */
trait MoveActionTrait
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-arrow-long-right');
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
        $defaultPath = $this->defaultPath();

        $fs = Storage::disk($this->disk());

        return $form
            ->schema([
                TextInput::make('path')
                    ->label(__('filament.actions.storage.move.fields.path.name'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->required()
                    ->rules(['required', 'string', 'doesnt_start_with:/', "ends_with:{$this->allowedFileExtension()}", new StorageFileDirectoryExistsRule($fs)])
                    ->default($defaultPath),
            ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @return BaseMoveAction
     */
    abstract protected function storageAction(BaseModel $model, array $fields): StorageAction;

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
