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

class UploadScriptAction extends UploadAction
{
    public static function getDefaultName(): ?string
    {
        return 'upload-script';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.upload.name'));

        $this->visible(Gate::allows('create', VideoScript::class));
    }

    public function getSchema(Schema $schema): Schema
    {
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
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $record, array $data): UploadScript
    {
        /** @var UploadedFile $file */
        $file = Arr::get($data, 'file');

        /** @var Video|null $video */
        $video = Video::query()->find(Arr::get($data, Video::ATTRIBUTE_ID));

        $path = explode($video->filename, $video->path())[0];

        return new UploadScript($file, $path, $video);
    }

    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }

    /**
     * Get the file validation rules.
     */
    protected function fileRules(): array
    {
        return [
            'required',
            FileRule::types('txt')->max(2 * 1024),
        ];
    }
}
