<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\Models\ActionResult;
use App\Actions\Models\BaseAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class BackfillImageAction.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseAction<TModel>
 */
abstract class BackfillImageAction extends BaseAction
{
    /**
     * Handle action.
     *
     * @return ActionResult
     *
     * @throws RequestException
     */
    public function handle(): ActionResult
    {
        if ($this->relation()->getQuery()->where(Image::ATTRIBUTE_FACET, $this->getFacet()->value)->exists()) {
            Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Image of Facet '{$this->getFacet()->value}'.");

            return new ActionResult(ActionStatus::SKIPPED());
        }

        $image = $this->getImage();

        if ($image !== null) {
            $this->attachImage($image);
        }

        if ($this->relation()->getQuery()->where(Image::ATTRIBUTE_FACET, $this->getFacet()->value)->doesntExist()) {
            return new ActionResult(
                ActionStatus::FAILED(),
                "{$this->label()} '{$this->getModel()->getName()}' has no {$this->getFacet()->description} Image after backfilling. Please review."
            );
        }

        return new ActionResult(ActionStatus::PASSED());
    }

    /**
     * Create Image from response.
     *
     * @param  string  $url
     * @return Image
     *
     * @throws RequestException
     */
    protected function createImage(string $url): Image
    {
        $imageResponse = Http::get($url)->throw();

        $image = $imageResponse->body();

        $file = File::createWithContent(basename($url), $image);

        $fs = Storage::disk('images');

        $fsFile = $fs->putFile($this->path(), $file);

        /** @var Image $image */
        $image = Image::query()->create([
            Image::ATTRIBUTE_FACET => $this->getFacet()->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        return $image;
    }

    /**
     * Path to storage image in filesystem.
     *
     * @return string
     */
    protected function path(): string
    {
        return Str::of(Str::kebab(class_basename($this->getModel())))
            ->append(DIRECTORY_SEPARATOR)
            ->append(Str::kebab($this->getFacet()->description))
            ->__toString();
    }

    /**
     * Attach Image to model.
     *
     * @param  Image  $image
     * @return void
     */
    abstract protected function attachImage(Image $image): void;

    /**
     * Get the facet to backfill.
     *
     * @return ImageFacet
     */
    abstract protected function getFacet(): ImageFacet;

    /**
     * Query third-party APIs to find Image.
     *
     * @return Image|null
     *
     * @throws RequestException
     */
    abstract protected function getImage(): ?Image;
}
