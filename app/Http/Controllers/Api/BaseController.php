<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Api\Query;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class BaseController.
 */
abstract class BaseController extends Controller
{
    /**
     * Resolves include paths and field sets.
     *
     * @var Query
     */
    protected Query $query;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $parameters = $request->only(Query::parameters());

        $this->query = Query::make($parameters);
    }
}
