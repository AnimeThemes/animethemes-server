<?php

namespace App\Contracts;

interface Streamable
{
    /**
     * Get path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Get MIME type.
     *
     * @return string
     */
    public function getMimetype();

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize();

    /**
     * Get name of storage disk.
     *
     * @return string
     */
    public function getDisk();
}
