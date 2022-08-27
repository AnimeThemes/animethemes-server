<?php

declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface Streamable.
 */
interface Streamable
{
    /**
     * Get the path of the streamable model in the filesystem.
     *
     * @return string
     */
    public function path(): string;

    /**
     * Get the basename of the streamable model.
     *
     * @return string
     */
    public function basename(): string;

    /**
     * Get the MIME type / content type of the streamable model.
     *
     * @return string
     */
    public function mimetype(): string;

    /**
     * Get the content length of the streamable model.
     *
     * @return int
     */
    public function size(): int;
}
