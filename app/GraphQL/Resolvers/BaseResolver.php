<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\GraphQL\Schema\Mutations\BaseMutation;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\ControllerMiddlewareOptions;
use Illuminate\Routing\FiltersControllerMiddleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseResolver
{
    final public const MODEL = 'model';

    /**
     * HTTP middleware stack that should be run before the resolver action.
     *
     * Each element is an array with keys `middleware` and `options`. The
     * `options` array is the same structure that the `ControllerMiddlewareOptions`
     * helper understands (see `only` / `except`). This mirrors the behaviour of
     * Laravel controllers so you can register middleware exactly the same way:
     *
     *     $this->middleware('auth')->only('store');
     *     $this->middleware(['auth', 'log'])->except('destroy');
     *
     * The resolver will evaluate the options against the current action name
     * when deciding which middleware to run.
     *
     * @var array<int, array{middleware:class-string|string,options:array}>
     */
    protected array $middleware = [];

    /**
     * Get the attributes and values that were validated.
     *
     * @param  class-string<BaseMutation>  $mutation
     * @return array<string, mixed>
     */
    public function validated(array $args, string $mutation): array
    {
        $mutationInstance = App::make($mutation);

        $validator = Validator::make($args, $mutationInstance->rulesForValidation($args));

        $validated = $validator->validated();

        return [
            ...$validated,

            'model' => Arr::get($args, self::MODEL),
        ];
    }

    /**
     * Run the middleware pipeline for the given action.
     */
    protected function runMiddleware(): void
    {
        $action = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        $stack = [];
        foreach ($this->middleware as $entry) {
            $options = $entry['options'] ?? [];

            if (FiltersControllerMiddleware::methodExcludedByOptions($action, $options)) {
                continue;
            }

            $stack[] = $entry['middleware'];
        }

        if ($stack === []) {
            return;
        }

        resolve(Pipeline::class)
            ->send(request())
            ->through($stack)
            ->thenReturn();
    }

    /**
     * Register middleware on the controller.
     *
     * @param  \Closure|array|string  $middleware
     * @return ControllerMiddlewareOptions
     */
    public function middleware($middleware, array $options = [])
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new ControllerMiddlewareOptions($options);
    }
}
