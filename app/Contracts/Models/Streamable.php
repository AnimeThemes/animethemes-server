<?php

declare(strict_types=1);

namespace App\Contracts\Models;

interface Streamable
{
    /**
     * Get the path of the streamable model in the filesystem.
     */
    public function path(): string;

    /**
     * Get the basename of the streamable model.
     */
    public function basename(): string;

    /**
     * Get the MIME type / content type of the streamable model.
     */
    public function mimetype(): string;

    /**
     * Get the content length of the streamable model.
     */
    public function size(): int;
}
