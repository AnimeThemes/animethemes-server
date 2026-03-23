<?php

declare(strict_types=1);

namespace App\Services\FlowframeTrend\Adapters;

abstract class AbstractAdapter
{
    abstract public function format(string $column, string $interval): string;
}
