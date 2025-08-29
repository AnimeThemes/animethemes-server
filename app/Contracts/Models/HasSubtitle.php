<?php

declare(strict_types=1);

namespace App\Contracts\Models;

interface HasSubtitle
{
    public function getSubtitle(): string;
}
