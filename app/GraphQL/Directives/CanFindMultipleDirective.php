<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use GraphQL\Error\Error;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Nuwave\Lighthouse\Auth\BaseCanDirective;
use Nuwave\Lighthouse\Exceptions\ClientSafeModelNotFoundException;
use Nuwave\Lighthouse\Exceptions\DefinitionException;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\SoftDeletes\ForceDeleteDirective;
use Nuwave\Lighthouse\SoftDeletes\RestoreDirective;
use Nuwave\Lighthouse\SoftDeletes\TrashedDirective;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Support\Utils;

class CanFindMultipleDirective extends BaseCanDirective
{
    public static function definition(): string
    {
        $commonArguments = BaseCanDirective::commonArguments();
        $commonTypes = BaseCanDirective::commonTypes();

        return /** @lang GraphQL */ <<<GRAPHQL
{$commonTypes}

"""
Check a Laravel Policy to ensure the current user is authorized to access a field.

Query for specific model instances to check the policy against, using primary key(s) from specified argument.
"""
directive @canFindMultiple(
{$commonArguments}

  """
  Specify the name of the field arguments that contains its columns[index].

  You may pass the string in dot notation to use nested inputs.
  """
  find: [String!]!

  """
  Specify the class name of the model to use.
  """
  models: [String!]!

  """
  Specify the name of the columns that reference the find argument.
  """
  columns: [String!]!

  """
  Should the query fail when the models of `find` were not found?
  """
  findOrFail: Boolean! = true

  """
  Apply scopes to the underlying query.
  """
  scopes: [String!]
) repeatable on FIELD_DEFINITION
GRAPHQL;
    }

    protected function authorizeRequest(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo, callable $resolver, callable $authorize): mixed
    {
        $ability = $this->directiveArgValue('ability');

        Gate::authorize($ability, [ASTHelper::modelName($this->definitionNode), ...$this->modelsToCheck($root, $args, $context, $resolveInfo)]);

        return null;
    }

    /**
     * @param  array<string, mixed>  $args
     * @return iterable<Model|class-string<Model>>
     */
    protected function modelsToCheck(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): iterable
    {
        $findList = $this->directiveArgValue('find');
        $findValues = [];
        foreach ($findList as $find) {
            $findValues[] = Arr::get($args, $find) ?? throw self::missingKeyToFindModel($find);
        }

        $models = [];
        foreach ($this->getModelClasses() as $modelClass) {
            $queryBuilder = $modelClass::query();

            $argumentSetDirectives = $resolveInfo->argumentSet->directives;
            $directivesContainsForceDelete = $argumentSetDirectives->contains(
                Utils::instanceofMatcher(ForceDeleteDirective::class),
            );
            if ($directivesContainsForceDelete) {
                /** @see \Illuminate\Database\Eloquent\SoftDeletes */
                // @phpstan-ignore-next-line because it involves mixins
                $queryBuilder->withTrashed();
            }

            $directivesContainsRestore = $argumentSetDirectives->contains(
                Utils::instanceofMatcher(RestoreDirective::class),
            );
            if ($directivesContainsRestore) {
                /** @see \Illuminate\Database\Eloquent\SoftDeletes */
                // @phpstan-ignore-next-line because it involves mixins
                $queryBuilder->onlyTrashed();
            }

            try {
                $enhancedBuilder = $resolveInfo->enhanceBuilder(
                    $queryBuilder,
                    $this->directiveArgValue('scopes', []),
                    $root,
                    $args,
                    $context,
                    $resolveInfo,
                    Utils::instanceofMatcher(TrashedDirective::class),
                );
                assert($enhancedBuilder instanceof EloquentBuilder);

                foreach ($findValues as $index => $findValue) {
                    if ($findValue instanceof Model) {
                        $models[] = $findValue;
                    } else {
                        $models[] = $this->directiveArgValue('findOrFail', false)
                            ? $enhancedBuilder->where($this->directiveArgValue('columns')[$index], $findValue)->firstOrFail()
                            : $enhancedBuilder->where($this->directiveArgValue('columns')[$index], $findValue)->first();
                    }
                }
            } catch (ModelNotFoundException $modelNotFoundException) {
                throw ClientSafeModelNotFoundException::fromLaravel($modelNotFoundException);
            }
        }

        return $models;
    }

    /**
     * Get the model class from the `model` argument of the field.
     *
     * @api
     *
     * @param  string  $argumentName  The default argument name "model" may be overwritten
     * @return class-string<Model>[]
     */
    protected function getModelClasses(string $argumentName = 'models'): array
    {
        $models = $this->directiveArgValue($argumentName, ASTHelper::modelName($this->definitionNode))
            ?? throw new DefinitionException("Could not determine a model name for the '@{$this->name()}' directive on '{$this->nodeName()}'.");

        $namespaces = [];
        foreach ($models as $model) {
            $namespaces[] = $this->namespaceModelClass($model);
        }

        return $namespaces;
    }

    public static function missingKeyToFindModel(string $find): Error
    {
        return new Error("Got no key to find a model at the expected input path: {$find}.");
    }
}
