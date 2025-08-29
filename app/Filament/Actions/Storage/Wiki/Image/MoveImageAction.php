<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Image;

use App\Actions\Storage\Wiki\Image\MoveImageAction as MoveImage;
use App\Constants\Config\ImageConstants;
use App\Filament\Actions\Storage\Base\MoveAction;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class MoveImageAction extends MoveAction
{
    public static function getDefaultName(): ?string
    {
        return 'move-image';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.image.move.name'));

        $this->visible(Gate::allows('create', Image::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Image  $image
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $image, array $data): MoveImage
    {
        /** @var string $path */
        $path = Arr::get($data, 'path');

        return new MoveImage($image, $path);
    }

    public function disk(): string
    {
        return Config::get(ImageConstants::DISKS_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     */
    protected function defaultPath(): ?string
    {
        $image = $this->getRecord();

        return $image instanceof Image
            ? $image->path
            : null;
    }

    /**
     * The file extension that the path must end with.
     */
    protected function allowedFileExtension(): string
    {
        return '.'.File::extension($this->defaultPath());
    }
}
