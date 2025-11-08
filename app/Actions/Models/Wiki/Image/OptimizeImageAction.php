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
    public function __construct(protected readonly Image $image, protected readonly string $extension = 'avif') {}

    /**
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::beginTransaction();

            $directory = File::dirname($this->image->path);

            $optimizedImage = $this->convertImage();

            if ($optimizedImage === null) {
                DB::rollBack();

                return new ActionResult(
                    ActionStatus::FAILED,
                    "Failed to convert image '{$this->image->path}' to '{$this->extension}'.",
                );
            }

            $path = $this->uploadImage($optimizedImage, $directory);

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

    /**
     * @throws Exception
     */
    protected function convertImage(): ?string
    {
        try {
            Storage::disk('local')->put(
                $this->image->path,
                Storage::disk(Config::get(ImageConstants::DISKS_QUALIFIED))->get($this->image->path),
            );

            [$command, $imagePath] = match ($this->extension) {
                'avif' => static::getAvifCommand($this->image),
                default => throw new Exception("Unsupported image extension: {$this->extension}"),
            };

            Process::run($command)->throw();

            // Delete the old image from the bucket.
            Storage::disk(Config::get(ImageConstants::DISKS_QUALIFIED))->delete($this->image->path);

            return $imagePath;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            Storage::disk('local')->delete($this->image->path);
        }

        return null;
    }

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
    public static function getAvifCommand(Image $image): array
    {
        $imagePath = Storage::disk('local')->path(
            Str::replaceLast(File::extension($image->path), 'avif', $image->path)
        );

        return [
            [
                'ffmpeg',
                '-i',
                Storage::disk('local')->path($image->path),
                '-c:v',
                'libaom-av1',
                '-crf',
                '30',
                '-pix_fmt',
                'yuv420p',
                $imagePath,
            ],
            $imagePath,
        ];
    }
}
