<?php

namespace App\ScoutElastic;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class ArtistIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}
