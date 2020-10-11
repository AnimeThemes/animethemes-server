<?php

namespace App\Grills;

use Faker\Factory;
use Illuminate\Support\Facades\Storage;

class GrillFactory
{
    public static function getGrill() : Grill
    {
        // Get grills through Storage Facade [see config/filesystems.php]
        $grill_disk = Storage::disk('grill');
        $grills = $grill_disk->files();

        // Get a random grill with Faker
        $faker = Factory::create();
        $grill = $faker->randomElement($grills);
        $grill_path = $grill_disk->url($grill);

        // Return Grill
        return new Grill($grill_path);
    }
}
