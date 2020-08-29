<?php

namespace App\ScoutElastic;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class ThemeIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}