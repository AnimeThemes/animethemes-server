<?php

namespace App\Grills;

class Grill
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Get grill path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
