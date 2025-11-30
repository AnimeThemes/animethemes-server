<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Image;

use App\Actions\ActionResult;
use App\Constants\Config\ImageConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Image;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OptimizeImageAction
{
    public function __construct(
        protected readonly Image $image,
        protected readonly ?string $extension = null,
        protected readonly ?int $width = null,
        protected readonly ?int $height = null,
    ) {}

    /**
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        if ($this->extension === null && $this->width === null && $this->height === null) {
            return new ActionResult(
                ActionStatus::SKIPPED,
                'No optimization parameters provided, nothing to process.'
            );
        }

        try {
            DB::beginTransaction();

            $directory = File::dirname($this->image->path);

            $optimizedImage = $this->handleFFmpeg();

            if ($optimizedImage === null) {
                DB::rollBack();

                return new ActionResult(
                    ActionStatus::FAILED,
                    "Failed to optimize image '{$this->image->path}'.",
                );
            }

            $path = $this->uploadImage(
                Storage::disk('local')->path($optimizedImage),
                $directory
            );

            // Delete the old image from the bucket.
            Storage::disk(Config::get(ImageConstants::DISKS_QUALIFIED))->delete($this->image->path);

            $this->image->update([
                Image::ATTRIBUTE_PATH => $path,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    protected function handleFFmpeg(): ?string
    {
        try {
            Storage::disk('local')->put(
                $this->image->path,
                Storage::disk(Config::get(ImageConstants::DISKS_QUALIFIED))->get($this->image->path),
            );

            $imagePath = $this->image->path;

            if ($this->extension !== null) {
                $imagePath = $this->convertImage($imagePath);
            }

            if ($this->width !== null && $this->height !== null) {
                return $this->downscaleImage($imagePath);
            }

            return $imagePath;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            Storage::disk('local')->delete($this->image->path);
        }

        return null;
    }

    /**
     * Convert the image to given extension.
     *
     * @throws Exception
     */
    protected function convertImage(string $path): string
    {
        [$command, $imagePath] = match ($this->extension) {
            'avif' => static::getAvifCommand($path),
            default => throw new Exception("Unsupported image extension: {$this->extension}"),
        };

        Process::run($command)->throw();

        return $imagePath;
    }

    /**
     * Downscale the image to given width and height.
     *
     * @throws Exception
     */
    protected function downscaleImage(string $path): string
    {
        [$command, $imagePath] = static::getDownscaleCommand($path, $this->width, $this->height);

        Process::run($command)->throw();

        return $imagePath;
    }

    /**
     * Upload the image to the bucket.
     */
    protected function uploadImage(string $image, string $directory): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $fs */
        $fs = Storage::disk(Config::get(ImageConstants::DISKS_QUALIFIED));

        $fsFile = $fs->putFile($directory, $image);

        Log::info("Uploading optimized Image {$fsFile}");

        return $fsFile;
    }

    /**
     * @return array{0:array<int, string>, 1:string}
     */
    public static function getAvifCommand(string $path): array
    {
        $imagePath = Storage::disk('local')->path(
            $newPath = Str::replaceLast(File::extension($path), 'avif', $path)
        );

        return [
            [
                'ffmpeg',
                '-i',
                Storage::disk('local')->path($path),
                '-c:v',
                'libaom-av1',
                '-crf',
                '30',
                '-pix_fmt',
                'yuv420p',
                $imagePath,
            ],
            $newPath,
        ];
    }

    /**
     * @return array{0:array<int, string>, 1:string}
     */
    public static function getDownscaleCommand(string $path, int $width = 100, int $height = 150): array
    {
        $tempPath = Storage::disk('local')->path(
            $newPath = Str::replaceLast('.', '_scaled.', $path)
        );

        return [
            [
                'ffmpeg',
                '-y',
                '-i',
                Storage::disk('local')->path($path),
                '-vf',
                "scale={$width}:{$height}",
                $tempPath,
            ],
            $newPath,
        ];
    }
}
