<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Observers\Wiki\PerformanceObserver;
use App\Scopes\PerformanceScope;
use Database\Factories\Wiki\PerformanceFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

/**
 * @method static PerformanceFactory factory(...$parameters)
 */
#[ObservedBy(PerformanceObserver::class)]
#[ScopedBy(PerformanceScope::class)]
class Performance extends SongStaff {}
