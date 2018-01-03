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

        // Step 1: Create video objects for new files
        $files = $fs->listContents('', true);
        foreach ($files as $file) {
            $isFile = $file['type'] == 'file';
            if ($isFile) {
                $video = Video::where('alias', $file['filename'])->where('path', $file['path'])->first();
                if (!$video) {
                    LOG::info('create video', ['alias' => $file['filename'], 'path' => $file['path']]);
                    Video::create(array(
                        'alias' => $file['filename'],
                        'path' => $file['path']
                    ));
                }
            }
        }

        // Step 2: Delete video objects for removed files
        $videos = Video::all();
        foreach ($videos as $video) {
            $flag = false;
            try {
                $metaData = $fs->getMetadata($video->path);
                $flag = !$metaData || $metaData['filename'] !== $video->alias || $metaData['path'] !== $video->path;
            } catch (FileNotFoundException $fnf) {
                $flag = true;
            } catch (RequestException $r) {
                $flag = true;
            }

            if ($flag) {
                LOG::info('delete video', ['alias' => $video->alias, 'path' => $video->path]);
                $video->delete();
            }
        }

        LOG::info('sync-videos end');
    }
}
