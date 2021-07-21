<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Class AppLayout.
 */
class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return View|Htmlable|Closure|string
     */
    public function render(): View | Htmlable | Closure | string
    {
        return view('layouts.app');
    }
}
