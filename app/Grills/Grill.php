<?php

namespace App\Grills;

class Grill {

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path) {
        $this->path = $path;
    }

    public function getPath() : string {
        return $this->path;
    }
}
