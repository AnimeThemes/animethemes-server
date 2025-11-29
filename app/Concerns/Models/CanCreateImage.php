<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Constants\Config\ImageConstants;
use App\Contracts\Models\HasImages;
use App\Enums\Models\Wiki\ImageFacet;
use App\Jobs\Wiki\Image\OptimizeImageJob;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait CanCreateImage
{
    use HasLabel;

    public function createImageFromUrl(string $url, ImageFacet $facet, (BaseModel&HasImages)|null $model = null): Image
    {
        $binary = Http::get($url)
            ->throw()
            ->body();

        $tmp = tempnam(sys_get_temp_dir(), 'img_');

        file_put_contents($tmp, $binary);

        $fsFile = Storage::disk(Config::get(ImageConstants::DISKS_QUALIFIED))
            ->putFile($this->path($facet, $model), new File($tmp));

        Log::info("Creating Image {$fsFile}");
        $image = Image::query()->create([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        $this->attachImage($image, $model);

        $this->optimize($image);

        return $image;
    }

    public function createImageFromFile(mixed $image, ImageFacet $facet, (BaseModel&HasImages)|null $model = null): Image
    {
        $fsFile = Storage::disk(Config::get(ImageConstants::DISKS_QUALIFIED))
            ->putFile($this->path($facet, $model), $image);

        Log::info("Creating Image {$fsFile}");
        $image = Image::query()->create([
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);

        $this->attachImage($image, $model);

        $this->optimize($image);

        return $image;
    }

    protected function path(ImageFacet $facet, (BaseModel&HasImages)|null $model): string
    {
        $path = Str::of('');

        if ($model !== null) {
            $path = $path
                ->append(Str::kebab(class_basename($model)))
                ->append(DIRECTORY_SEPARATOR);
        }

        return $path
            ->append(Str::kebab($facet->localize()))
            ->__toString();
    }

    protected function attachImage(Image $image, (BaseModel&HasImages)|null $model): void
    {
        if ($model !== null) {
            Log::info("Attaching Image {$image->getName()} to {$this->privateLabel($model)} {$model->getName()}");
            $model->images()->attach($image);
        }
    }

    protected function optimize(Image $image): void
    {
        if ($image->facet === ImageFacet::SMALL_COVER) {
            OptimizeImageJob::dispatch($image, 'avif', 100, 150)
                ->onQueue('optimize-image')
                ->afterCommit();
        }
    }
}
