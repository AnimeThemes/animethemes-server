<?php

use App\DataManager\Mappings\KitsuMapper;
use Illuminate\Database\Seeder;

class KitsuSeeder extends Seeder 
{   
    public function run() {
        KitsuMapper::FillDatabase();
    }
}
