<?php

declare(strict_types=1);

namespace App\GraphQL\Handler;

use Error as PhpError;
use Exception;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error as GraphQLError;
use GraphQL\Error\FormattedError;
use GraphQL\Server\RequestError;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Error\ProvidesErrorCategory;
use Rebing\GraphQL\Error\ValidationError;
use Throwable;

class ErrorHandler
{
    public static function handleErrors(array $errors, callable $formatter): array
    {
        $handler = app()->make(ExceptionHandler::class);

        foreach ($errors as $error) {
            // Try to unwrap exception
            $error = $error->getPrevious() ?: $error;
            // Don't report certain GraphQL errors
            if ($error instanceof ValidationError) {
                continue;
            }
            if ($error instanceof AuthorizationError) {
                continue;
            }
            if ($error instanceof GraphQLError) {
                continue;
            }
            if ($error instanceof RequestError) {
                continue;
            }
            if (! $error instanceof Exception && ! $error instanceof PhpError) {
                continue;
            }

            if (! $error instanceof Exception) {
                $error = new Exception(
                    $error->getMessage(),
                    $error->getCode(),
                    $error
                );
            }

            $handler->report($error);
        }

        return array_map($formatter, $errors);
    }

    // /**
    //  * @return array<string,mixed>
    //  * @see ExecutionResult::setErrorFormatter
    //  */
    public static function formatError(GraphQLError $e): array
    {
        $debug = Config::get('app.debug') ? (DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE) : DebugFlag::NONE;
        $formatter = FormattedError::prepareFormatter(null, $debug);
        $error = $formatter($e);

        $previous = $e->getPrevious();

        if ($previous instanceof Throwable) {
            if ($previous instanceof ModelNotFoundException) {
                $error['message'] = $previous->getMessage();
                $error['extensions'] = [
                    'category' => 'model_not_found',
                    'model' => $previous->getModel(),
                ];
            }

            if ($previous instanceof ValidationException) {
                $error['message'] = 'validation';
                $error['extensions'] = [
                    'category' => 'validation',
                    'validation' => $previous->validator->errors()->getMessages(),
                ];
            }

            if ($previous instanceof ValidationError) {
                $error['extensions']['validation'] = $previous->getValidatorMessages()->getMessages();
            }

            if ($previous instanceof ProvidesErrorCategory) {
                $error['extensions']['category'] = $previous->getCategory();
            }
        } elseif ($e instanceof ProvidesErrorCategory) {
            $error['extensions']['category'] = $e->getCategory();
        }

        return $error;
    }
}
