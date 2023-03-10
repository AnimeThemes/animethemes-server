<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use App\Enums\BaseEnum;
use Illuminate\Support\Str;

/**
 * Class CrudPermissions.
 *
 * @method static static CREATE()
 * @method static static DELETE()
 * @method static static UPDATE()
 * @method static static VIEW()
 */
class CrudPermission extends BaseEnum
{
    public const CREATE = 'create';

    public const DELETE = 'delete';

    public const UPDATE = 'update';

    public const VIEW = 'view';

    /**
     * Format permission name for model.
     *
     * @param  string  $modelClass
     * @return string
     */
    public function format(string $modelClass): string
    {
        return Str::of($this->value)
            ->append(class_basename($modelClass))
            ->snake(' ')
            ->__toString();
    }
}
