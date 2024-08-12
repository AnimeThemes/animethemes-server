<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Exception\Pages;

use App\Filament\Resources\Admin\Exception;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages\ViewException as BaseViewException;

/**
 * Class ViewException.
 */
class ViewException extends BaseViewException
{
    protected static string $resource = Exception::class;
}
