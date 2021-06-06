<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use SMartins\Exceptions\JsonHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
    use JsonHandler;

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($request->expectsJson() && is_a($e, Exception::class)) {
            return $this->jsonResponse($e);
        }

        return parent::render($request, $e);
    }
}
