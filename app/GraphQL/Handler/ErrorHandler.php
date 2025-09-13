<?php

namespace App\GraphQL\Handler;

use Error as PhpError;
use Exception;
use GraphQL\Error\Error as GraphQLError;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Rebing\GraphQL\Error\AuthorizationError;
use Rebing\GraphQL\Error\ValidationError;

class ErrorHandler
{
    public static function handleErrors(array $errors, callable $formatter): array
    {
        $handler = app()->make(ExceptionHandler::class);

        foreach ($errors as $error) {
            // Try to unwrap exception
            $error = $error->getPrevious() ?: $error;

            // Don't report certain GraphQL errors
            if ($error instanceof ValidationError ||
                $error instanceof AuthorizationError ||
                $error instanceof GraphQLError ||
                !(
                    $error instanceof Exception ||
                    $error instanceof PhpError
                )) {
                continue;
            }

            if (!$error instanceof Exception) {
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
}
