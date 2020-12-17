<?php

namespace App\Grills;

use Illuminate\Support\Facades\Storage;

class Grill
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $url;

    /**
     * @param string $path
     * @param string $url
     */
    public function __construct($path, $url)
    {
        $this->path = $path;
        $this->url = $url;
    }

    /**
     * Get random grill from storage.
     *
     * @return Grill|null
     */
    public static function random()
    {
        $grill_disk = Storage::disk('grill');
        $grills = $grill_disk->files();

        if (empty($grills)) {
            return null;
        }

        $grill_path = collect($grills)->random();
        $grill_url = $grill_disk->url($grill_path);

        return new Grill($grill_path, $grill_url);
    }
}
