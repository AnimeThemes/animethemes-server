<?php

declare(strict_types=1);

namespace App\Contracts\Models;

interface Streamable
{
    public function path(): string;

    public function basename(): string;

    public function mimetype(): string;

    public function size(): int;
}
