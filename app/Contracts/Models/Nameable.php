<?php

declare(strict_types=1);

namespace App\Contracts\Models;

interface Nameable
{
    public function getName(): string;
}
