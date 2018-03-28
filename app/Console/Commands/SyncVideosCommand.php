<?php

namespace App\Console\Commands;

use App\Models\Video;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;

class SyncVideosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync video database table to object storage content';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        LOG::info('sync-videos start');
        $fs = Storage::disk('spaces');

        // Step 1: Delete video objects for removed files
        $videos = Video::all();
        foreach ($videos as $video) {
            $flag = false;
            try {
                $metaData = $fs->getMetadata($video->path);
                $flag = !$metaData || $metaData['basename'] !== $video->basename || $metaData['filename'] !== $video->filename || $metaData['path'] !== $video->path;
            } catch (FileNotFoundException $fnf) {
                $flag = true;
            } catch (RequestException $r) {
                Log::error('verify video', ['basename' => $video->basename, 'filename' => $video->filename, 'path' => $video->path]);
            }

            if ($flag) {
                LOG::info('delete video', ['basename' => $video->basename, 'filename' => $video->filename, 'path' => $video->path]);
                $video->delete();
            }
        }

        // Step 2: Create video objects for new files
        $files = $fs->listContents('', true);
        foreach ($files as $file) {
            $isFile = $file['type'] == 'file';
            if ($isFile) {
                $video = Video::where('basename', $file['basename'])->where('filename', $file['filename'])->where('path', $file['path'])->first();
                if (!$video) {
                    LOG::info('create video', ['basename' => $file['basename'], 'filename' => $file['filename'], 'path' => $file['path']]);
                    Video::create(array(
                        'basename' => $file['basename'],
                        'filename' => $file['filename'],
                        'path' => $file['path']
                    ));
                }
            }
        }

        LOG::info('sync-videos end');
    }
}
