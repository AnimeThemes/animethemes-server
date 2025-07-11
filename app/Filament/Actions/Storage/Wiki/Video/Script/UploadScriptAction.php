<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction as UploadScript;
use App\Constants\Config\VideoConstants;
use App\Filament\Actions\Storage\Base\UploadAction;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Video\Script\Pages\ListScripts;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File as FileRule;

/**
 * Class UploadScriptAction.
 */
class UploadScriptAction extends UploadAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'upload-script';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.upload.name'));

        $this->visible(Gate::allows('create', VideoScript::class));
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Schema  $schema
     * @return Schema
     */
    public function getSchema(Schema $schema): Schema
    {
        $model = $this->getRecord();

        return $schema
            ->components([
                ...parent::getSchema($schema)->getComponents(),

                Hidden::make(Video::ATTRIBUTE_ID)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->default(fn (BaseRelationManager|ListScripts $livewire) => $livewire instanceof BaseRelationManager ? $livewire->getOwnerRecord()->getKey() : null),
            ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Model|null  $model
     * @param  array  $fields
     * @return UploadScript
     */
    protected function storageAction(?Model $model, array $fields): UploadScript
    {
        /** @var UploadedFile $file */
        $file = Arr::get($fields, 'file');

        /** @var Video|null $video */
        $video = Video::query()->find(Arr::get($fields, Video::ATTRIBUTE_ID));

        $path = explode($video->filename, $video->path())[0];

        return new UploadScript($file, $path, $video);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    protected function fileRules(): array
    {
        return [
            'required',
            FileRule::types('txt')->max(2 * 1024),
        ];
    }
}
