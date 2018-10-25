<?php

use App\DataManager\Mappings\AnilistMapper;
use Illuminate\Database\Seeder;

class AnilistSeeder extends Seeder 
{   
    public function run() {
        AnilistMapper::FillDatabase();
    }
}
