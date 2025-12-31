<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Image;

use App\Actions\Storage\Base\MoveAction;
use App\Constants\Config\ImageConstants;
use App\Models\Wiki\Image;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * @extends MoveAction<Image>
 */
class MoveImageAction extends MoveAction
{
    /**
     * The list of disk names.
     */
    public function disks(): array
    {
        return Arr::wrap(Config::get(ImageConstants::DISKS_QUALIFIED));
    }

    /**
     * Get the path to move from.
     */
    protected function from(): string
    {
        return $this->model->path;
    }

    /**
     * Update underlying model.
     * We want to apply these updates through Eloquent to preserve relations when renaming.
     * Otherwise, reconciliation would destroy the old model and create a new model for the new name.
     */
    protected function update(): Image
    {
        $this->model->update([
            Image::ATTRIBUTE_PATH => $this->to,
        ]);

        return $this->model;
    }
}
