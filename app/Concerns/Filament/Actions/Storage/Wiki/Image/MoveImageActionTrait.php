<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Storage\Wiki\Image;

use App\Actions\Storage\Wiki\Image\MoveImageAction as MoveImage;
use App\Constants\Config\ImageConstants;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

/**
 * Trait MoveImageActionTrait.
 */
trait MoveImageActionTrait
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.image.move.name'));

        $this->authorize('create', Image::class);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Image  $image
     * @param  array  $fields
     * @return MoveImage
     */
    protected function storageAction(BaseModel $image, array $fields): MoveImage
    {
        /** @var string $path */
        $path = Arr::get($fields, 'path');

        return new MoveImage($image, $path);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(ImageConstants::DISKS_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     *
     * @return string|null
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
     *
     * @return string
     */
    protected function allowedFileExtension(): string
    {
        return '.'.File::extension($this->defaultPath());
    }
}
