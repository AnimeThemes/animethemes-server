<?php

declare(strict_types=1);

namespace App\Services\FlowframeTrend;

class TrendValue
{
    public function __construct(
        public string $date,
        public mixed $aggregate,
    ) {}
}
